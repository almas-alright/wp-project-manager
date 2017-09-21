<?php

namespace CPM\Comment\Controllers;

use WP_REST_Request;
use League\Fractal;
use League\Fractal\Resource\Item as Item;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use CPM\Transformer_Manager;
use CPM\Comment\Transformers\Comment_Transformer;
use CPM\Common\Traits\Request_Filter;
use CPM\Comment\Models\Comment;

class Comment_Controller {
    use Transformer_Manager, Request_Filter;

    public function index( WP_REST_Request $request ) {
        $on = $request->get_param( 'on' );
        $id = $request->get_param( 'id' );
        $by = $request->get_param( 'by' );

        if ( $on ) {
            $query = Comment::where('commentable_type', $on);
        }

        if ( $id ) {
            $query = $query->where('commentable_id', $id);
        }

        if ( $by ) {
            $query = $query->where('created_by', $by);
        }

        if ( $query ) {
            $comments = $query->paginate();
        } else {
            $comments = Comment::paginate();
        }

        $comment_collection = $comments->getCollection();

        $resource = new Collection( $comment_collection, new Comment_Transformer );
        $resource->setPaginator( new IlluminatePaginatorAdapter( $comments ) );

        return $this->get_response( $resource );
    }

    public function show( WP_REST_Request $request ) {
        $comment_id = $request->get_param( 'comment_id' );
        $comment    = Comment::find( $comment_id );
        $resource   = new Item( $comment, new Comment_Transformer );

        return $this->get_response( $resource );
    }

    public function store( WP_REST_Request $request ) {
        $data     = $this->extract_non_empty_values( $request );
        $comment  = Comment::create( $data );
        $resource = new Item( $comment, new Comment_Transformer );

        return $this->get_response( $resource );
    }

    public function update( WP_REST_Request $request ) {
        $data       = $this->extract_non_empty_values( $request );
        $comment_id = $request->get_param( 'comment_id' );
        $comment    = Comment::find( $comment_id );
        $comment->update( $data );

        $resource = new Item( $comment, new Comment_Transformer );

        return $this->get_response( $resource );
    }

    public function destroy( WP_REST_Request $request ) {
        $comment_id = $request->get_param( 'comment_id' );
        $comment    = Comment::find( $comment_id );

        $comment->replies()->delete();
        $comment->files()->delete();
        $comment->delete();
    }
}