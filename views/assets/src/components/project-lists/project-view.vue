<template>

    <ul class="pm-project-view ">
        <li><a href="javascript:void(0)" :title="list_view" class="change-view" @click.prevent="setcookie('list_view')">  <span class=" dashicons dashicons-menu" v-bind:class="{'active': activeClass('list_view') }" ></span></a></li>
        <li><a href="javascript:void(0)"  :title="grid_view" class="change-view" @click.prevent="setcookie('grid_view')"> <span class=" dashicons dashicons-screenoptions" v-bind:class="{'active': activeClass('grid_view') }" ></span></a>
        </li>
        <div class="clearfix"></div>
    </ul>
</template>

<script>
    import Mixins from './mixin';
    
    export default{
        data(){
            return {
                list_view: __( 'List View', 'wedevs-project-manager'),
                grid_view: __( 'Grid View', 'wedevs-project-manager'),
                //projects_view: this.$store.state.projects_view,
            }
        },

        mixins: [Mixins],

        created(){
            this.getCookie("project_view");
            
        },
        methods: {
            ...pm.Vuex.mapMutations( 'projectLists',
                [
                    'setProjectsView'
                ]
            ),
            setcookie(name="grid_view"){
                var d = new Date();
                d.setTime(d.getTime() + (365 * 24 * 60 * 60 * 1000));
                var expires = "expires="+d.toUTCString();

                document.cookie = "project_view="+name + ';' + expires;
                this.setProjectsView(name);
                //this.$store.commit('projectLists/setProjectsView', name);
            },

            getCookie(key){
                var cookies = document.cookie.split(';'),
                 cookieslen = cookies.length;
                 key=key + "=";
                 for(var i =0; i<cookieslen; i++){
                    var c = cookies[i];
                    while (c.charAt(0) == ' ') {
                        c = c.substring(1);
                    }

                    if (c.indexOf(key) == 0) {
                        this.setProjectsView( c.substring(key.length, c.length) );
                        //this.$store.commit('projectLists/setProjectsView', c.substring(key.length, c.length))
                        return c.substring(key.length, c.length);
                    }
                 }

                 return "";
            },
            activeClass(view){
                if ( view == this.projects_view ) {
                    return view;
                }
                
                //return this.$store.state.projects_view === view;
            }
        }
    }
</script>