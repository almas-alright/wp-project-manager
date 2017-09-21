import Vue from './../../vue/vue';

export default Vue.mixin({
	methods: {
		showHideMilestoneForm (status, milestone) {
			var milestone   = milestone || false,
			    milestone   = jQuery.isEmptyObject(milestone) ? false : milestone;

			if ( milestone ) {
			    if ( status === 'toggle' ) {
			        milestone.edit_mode = milestone.edit_mode ? false : true;
			    } else {
			        milestone.edit_mode = status;
			    }
			} else {
			    this.$store.commit('showHideMilestoneForm', status);
			}
		},

		showHideCommentForm (status, comment) {
			if ( status === 'toggle' ) {
		        comment.edit_mode = comment.edit_mode ? false : true;
		    } else {
		        comment.edit_mode = status;
		    }
		},


	    getMilestone (self) {
	        var request = {
	            url: self.base_url + '/cpm/v2/projects/'+self.project_id+'/milestones/'+self.$route.params.discussion_id+'?with=comments',
	            success (res) {
	            	self.addMeta(res.data);
	                self.$store.commit( 'setMilestone', res.data );
	            }
	        };
	        self.httpRequest(request);
	    },

	    getMilestones (self) {
            var request = {
                url: self.base_url + '/cpm/v2/projects/'+self.project_id+'/milestones',
                success (res) {
                    self.$store.commit( 'setMilestones', res.data );
                }
            };
            self.httpRequest(request);
        },

	    addMeta (milestone) {
	    	milestone.edit_mode = false;
	    },

	    /**
	     * Insert and edit task
	     * 
	     * @return void
	     */
	    newMilestone: function() {
	        // Exit from this function, If submit button disabled 
	        if ( this.submit_disabled ) {
	            return;
	        }
	        
	        // Disable submit button for preventing multiple click
	        this.submit_disabled = true;

	        var self      = this,
	            is_update = typeof this.milestone.id == 'undefined' ? false : true,
	            form_data = {
	                title: this.milestone.title,
	                description: this.milestone.description,
	                order: '',
	                milestone: 4
	            };
	        
	        // Showing loading option 
	        this.show_spinner = true;

	        if (is_update) {
				var url  = self.base_url + '/cpm/v2/projects/'+self.project_id+'/milestones/'+this.milestone.id;
				var type = 'PUT'; 
	        } else {
				var url  = self.base_url + '/cpm/v2/projects/'+self.project_id+'/milestones';
				var type = 'POST';
	        }

	        var request_data = {
	            url: url,
	            type: type,
	            data: form_data,
	            success (res) {
	            	
	            	self.getMilestones(self);
	            	
	                self.show_spinner = false;

	                // Display a success toast, with a title
	                toastr.success(res.data.success);
	           
	                self.submit_disabled = false;
	                
	                if (is_update) {

	                	self.showHideMilestoneForm(false, self.milestone);
	                } else {
	                	self.showHideMilestoneForm(false);
	                }
	            },

	            error (res) {
	                self.show_spinner = false;
	                
	                // Showing error
	                res.data.error.map( function( value, index ) {
	                    toastr.error(value);
	                });
	                self.submit_disabled = false;
	            }
	        }

	        self.httpRequest(request_data);
	    }
	},
});