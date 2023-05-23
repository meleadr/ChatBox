import { createRouter, createWebHistory } from "vue-router";

// Pages
import Login from "../components/pages/Login.vue";
import Dashboard from "../components/pages/Dashboard.vue";
import ChatRoom from "../components/pages/ChatRoom.vue";

// NotFound
import NotFound from "../components/NotFound.vue";

const routes = [
	{
		path: "/",
		Name: "Login",
		component: Login,
	},
	{
		path: "/dashboard",
		Name: "Dashboard",
		component: Dashboard,
		meta: {
			requiresAuth: true,
		},
	},
	{
		path: "/:pathMatch(.*)*",
		Name: "NotFound",
		component: NotFound,
	},
];

const router = createRouter({
	history: createWebHistory(),
	routes,
});

router.beforeEach((to, from) => {
	if (to.meta.requiresAuth && !localStorage.getItem("token")) {
		return "/";
	}
});

export default router;
