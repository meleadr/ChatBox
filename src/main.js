import { createApp } from "vue";

import App from "./App.vue";

import router from "./router";
import "./style.css";

// 3. On monte l'application Vue sur l'élément #app
createApp(App).use(router).mount("#app");
