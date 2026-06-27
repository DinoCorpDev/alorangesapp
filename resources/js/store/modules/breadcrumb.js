const state = {
  items: JSON.parse(localStorage.getItem("breadcrumbItems")) || [],
};

const mutations = {
  SET_BREADCRUMB_ITEMS(state, items) {
    state.items = items;
    localStorage.setItem("breadcrumbItems", JSON.stringify(state.items));
  },
  ADD_BREADCRUMB_ITEM(state, item) {
    state.items.push(item);
    localStorage.setItem("breadcrumbItems", JSON.stringify(state.items));
  },
  RESET_BREADCRUMB_ITEMS(state) {
    state.items = [];
    localStorage.setItem("breadcrumbItems", JSON.stringify(state.items));
  }
};

const actions = {
  setBreadcrumbItems({ commit }, items) {
    commit("SET_BREADCRUMB_ITEMS", items);
  },
  addBreadcrumbItem({ commit }, item) {
    commit("ADD_BREADCRUMB_ITEM", item);
  },
  resetBreadcrumbItems({ commit }) {
    commit("RESET_BREADCRUMB_ITEMS");
  }
};

const getters = {
  breadcrumbItems: (state) => state.items,
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
};