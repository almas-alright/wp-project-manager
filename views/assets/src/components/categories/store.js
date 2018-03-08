
/**
 * Make sure to call pm.Vue.use(Vuex) first if using a vuex module system
 */
export default {

    state: {
        isFetchCategories: false,
        categories: [],
        getIndex: function ( itemList, id, slug) {
            var index = false;

            itemList.forEach(function(item, key) {
                if (item[slug] == id) {
                    index = key;
                }
            });

            return index;
        },
    }, 
    mutations: {
        afterNewCategories (state, categories) {
            state.categories.push(categories);
        },

        setCategories (state, categories) {
            state.categories = categories;
            state.isFetchCategories = true;
        },

        afterUpdateCategories (state, category) {
            var category_index = state.getIndex(state.categories, category.id, 'id');
            state.categories.splice(category_index,1, category);
        },
        afterDeleteCategory (state, id) {
            var category_index = state.getIndex(state.categories, id, 'id');
            state.categories.splice(category_index,1);
        }
    }
};