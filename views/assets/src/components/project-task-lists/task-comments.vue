<template>
    <div class="pm-task-comment-wrap">
        
        <h3 class="pm-comment-title">{{ __( 'Discuss this task', 'wedevs-project-manager') }}</h3>

        <ul class="pm-comment-wrap" v-if="comments.length">
            <li  v-for="comment in comments" :key="'tasks-comments-'+comment.id" :class="'pm-comment clearfix even pm-fade-out-'+comment.id">

                <div class="pm-avatar">
                     <a :href="myTaskRedirect( comment.creator.data.id )" :title="comment.creator.data.display_name"><img :alt="comment.creator.data.display_name" :src="comment.creator.data.avatar_url" class="avatar avatar-96 photo" height="96" width="96"></a>
                </div>

                <div class="pm-comment-container">
                    <div class="pm-comment-meta">
                        {{__( 'By', 'wedevs-project-manager')}}
                        <span class="pm-author">
                            <a :href="myTaskRedirect( comment.creator.data.id )" :title="comment.creator.data.display_name">
                                {{ comment.creator.data.display_name }}
                            </a>
                        </span>
                        <span>{{ __('on', 'wedevs-project-manager')}}</span>
                        <span class="pm-date">
                            <time :datetime="dateISO8601Format( comment.comment_date )" :title="dateISO8601Format( comment.comment_date )">{{ commentDate(comment) }}</time>
                        </span>
                        <!-- v-if="current_user_can_edit_delete(comment, task)" -->
                        <div  class="pm-comment-action" v-if="can_edit_comment(comment)" >
                            <span class="pm-edit-link">
                                <a href="#" @click.prevent="showHideTaskCommentForm( comment )" class="dashicons dashicons-edit"></a>
                            </span>

                            <span class="pm-delete-link">
                                <a href="#" @click.prevent="deleteTaskComment( comment.id )" class="dashicons dashicons-trash"></a>
                            </span>
                        </div>
                    </div>

                    <div class="pm-comment-content">
                        <div v-html="comment.content"></div>
                        <ul class="pm-attachments" v-if="comment.files.data.length">
                            <li v-for="file in comment.files.data">
                                <a v-if="file.type == 'image'" v-pm-pretty-photo class="pm-colorbox-img" :href="getDownloadUrl(file.attachment_id)" :title="file.name" target="_blank">
                                    <img class="pm-content-img-size" :src="file.thumb" :alt="file.name">
                                </a>

                                <a v-else class="pm-colorbox-img" :href="getDownloadUrl(file.attachment_id)" :title="file.name" target="_blank">
                                    <img class="pm-content-img-size" :src="file.thumb" :alt="file.name">
                                </a>
                            </li>
                        </ul>
                    </div>
                    <transition name="slide" v-if="can_edit_comment(comment)" >
                        <div class="pm-comment-edit-form" v-if="comment.edit_mode">
                            <task-comment-form :task="task" :comment="comment" :comments="comments"></task-comment-form>
                        </div>
                    </transition>
                </div>
            </li>
        </ul>
        <div class="single-todo-comments">
            <div class="pm-comment-form-wrap">

                <div class="pm-avatar">
                    <a :href="myTaskRedirect(PM_Vars.current_user.ID)">
                        <img :src="avatar_url" height="48" width="48"/>
                    </a>
                </div>
                <div class="pm-new-doc-comment-form">
                    <task-comment-form :task="task" :comment="{}" :comments="comments"></task-comment-form>
                </div><!--v-end--><!--v-component-->
            </div>
        </div>
    </div>
</template>

<style>
    .pm-task-comment-thumb {
        height: 80px;
        width: 80px;
    }
</style>

<script>
    import comment_form from './task-comment-form.vue';
    import Mixins from './mixin';
    
    export default {
        // Get passing data for this component.
        props: ['comments', 'task'],

        mixins: [Mixins],

        data: function() {
            return {
                currnet_user_id: 1,
                avatar_url: PM_Vars.avatar_url
            }
        },

        components: {
            'task-comment-form': comment_form
        },

        methods: {
            commentDate (comment) {
                if (typeof comment.created_at != 'undefined') {
                    return comment.created_at.date + ', ' + comment.created_at.time;
                }

                return '';
            },
            showHideTaskCommentForm (comment) {
                comment.edit_mode = comment.edit_mode ? false : true;
            },
            current_user_can_edit_delete: function( comment, task ) {
                if ( comment.comment_type == 'pm_activity' ) {
                    return false;
                }

                if ( task.can_del_edit ) {
                    return true;
                }

                if ( (comment.user_id == this.currnet_user_id ) && (comment.comment_type == '') ) {
                    return true;
                }

                return false;
            },

            deleteTaskComment (id) {
                if ( !confirm( this.__( 'Are you sure!', 'wedevs-project-manager') ) ) {
                    return;
                }
                var self = this;

                var request_data = {
                    url: self.base_url + '/pm/v2/projects/'+self.project_id+'/comments/' + id + '/delete',
                    type: 'POST',
                    success (res) {
                        var index = self.getIndex(self.comments, id, 'id');
                        pm.Toastr.success(res.message);
                        self.comments.splice(index, 1);
                    }
                }
                this.httpRequest(request_data);
            }

        }
    }
</script>
