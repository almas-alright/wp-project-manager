<template>
	<form class="pm-comment-form-vue" @submit.prevent="taskCommentAction()">

        <div class="item message pm-sm-col-12 ">
            <text-editor :editor_id="editor_id" :content="content"></text-editor>
        </div>

         <file-uploader :files="files" :delete="deleted_files"></file-uploader>
         <notify-user v-model="notify_users"></notify-user>
               
        <div class="submit">
            <input v-if="!comment.edit_mode" :disabled="submit_disabled" type="submit" class="button-primary"  :value="text.add_new_comment" id="" />
            <input v-if="comment.edit_mode" :disabled="submit_disabled" type="submit" class="button-primary"  :value="text.update_comment" id="" />
            <span v-show="show_spinner" class="pm-spinner"></span>
        </div>
    </form>
</template>

<script>
	import editor from '@components/common/text-editor.vue';
	import uploader from '@components/common/file-uploader.vue';
  import notifyUser from '@components/common/notifyUser.vue';

	export default {
		props: ['comment', 'comments'],
		data () {
			return {
				submit_disabled: false,
				show_spinner: false,
				content: {
	                html: typeof this.comment.content == 'undefined' ? '' : this.comment.content,
	            },
        task_id: this.$route.params.task_id,
        files: typeof this.comment.files === 'undefined' ? [] : this.comment.files.data,
				deleted_files: [],
        notify_users: [],
			}
		},
		components: {
			'text-editor': editor,
			'file-uploader': uploader,
      notifyUser: notifyUser
		},

		watch: {
			/**
	         * Observe onchange comment message
	         *
	         * @param string new_content 
	         * 
	         * @type void
	         */
	        content: {
	            handler: function( new_content ) {
	                this.comment.content = new_content.html;
	            },

	            deep: true
	        },
		},

		computed: {
			/**
	         * Editor ID
	         * 
	         * @return string
	         */
	        editor_id: function() {
	            var comment_id = ( typeof this.comment.id === 'undefined' ) ? '' : '-' + this.comment.id;
	            return 'pm-comment-editor' + comment_id;
	        },
		},
		methods: {

			taskCommentAction () {
   			// Prevent sending request when multiple click submit button 
        if ( this.submit_disabled ) {
            return;
        }

         // Disable submit button for preventing multiple click
        this.submit_disabled = true;
        // Showing loading option 
        this.show_spinner = true;
        var self = this;

        var args = {
        	data: {
              commentable_id: self.task_id,
              content: self.comment.content,
              commentable_type: 'task',
              deleted_files: self.deleted_files || [],
              files: self.files || [],
              notify_users: this.notify_users
            },
        }

        if(typeof this.comment.id !== 'undefined' ){
        	args.data.id = this.comment.id;
        	args.callback = function(res){
        		var index = self.getIndex( self.comments, self.comment.id, 'id' );
              self.comments.splice(index, 1, res.data);

        		self.submit_disabled = false;
        		self.show_spinner = false;
        		self.files = []; self.deleted_files = [];
        	}

        	self.updateComment ( args );
        }else{

        	args.callback = function ( res ) {
        		self.comments.splice(0, 0, res.data);
        		self.submit_disabled = false;
        		self.show_spinner = false;
        		self.files = []; self.deleted_files = [];
        	}
        	self.addComment ( args );
        }
   		}
		}
	}
</script>