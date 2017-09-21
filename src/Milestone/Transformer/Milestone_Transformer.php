<?php

namespace CPM\Milestone\Transformer;

use CPM\Milestone\Models\Milestone;
use League\Fractal\TransformerAbstract;
use CPM\Task_List\Transformer\Task_List_Transformer;
use CPM\Task\Transformer\Task_Transformer;
use CPM\Discussion_Board\Transformer\Discussion_Board_Transformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class Milestone_Transformer extends TransformerAbstract {
    protected $availableIncludes = [
        'discussion_boards', 'task_lists'
    ];

    public function transform( Milestone $item ) {
        return [
            'id'          => (int) $item->id,
            'title'       => $item->title,
            'description' => $item->description,
            'order'       => $item->order,
            'achieve_date'=> format_date( $item->achieve_date ),
            'created_by'  => $item->created_by,
            'updated_by'  => $item->updated_by,
            'meta' => [
                'total_task_list' => $item->task_lists->count(),
                'total_discussion_board' => $item->discussion_boards->count(),
            ],
        ];
    }

    public function includeTaskLists( Milestone $item ) {
        $page = isset( $_GET['task_list_page'] ) ? $_GET['task_list_page'] : 1;

        $task_lists = $item->task_lists()
            ->orderBy( 'created_at', 'DESC' )
            ->paginate( 10, ['*'], 'task_list_page', $page );

        $task_list_collection = $task_lists->getCollection();
        $resource = $this->collection( $task_list_collection, new Task_List_Transformer );

        $resource->setPaginator( new IlluminatePaginatorAdapter( $task_lists ) );

        return $resource;
    }

    public function includeDiscussionBoards( Milestone $item ) {
        $page = isset( $_GET['discussion_page'] ) ? $_GET['discussion_page'] : 1;

        $discussion_boards = $item->discussion_boards()
            ->orderBy( 'created_at', 'DESC' )
            ->paginate( 10, ['*'], 'discussion_page', $page );

        $discussion_board_collection = $discussion_boards->getCollection();
        $resource = $this->collection( $discussion_board_collection, new Discussion_Board_Transformer );

        $resource->setPaginator( new IlluminatePaginatorAdapter( $discussion_boards ) );

        return $resource;
    }
}