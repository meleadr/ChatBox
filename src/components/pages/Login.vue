<template>
	<h1>Connexion</h1>
	<div class="card">
		<form id="login-form" @submit.prevent="login">
			<label for="email">Email</label>
			<input
				type="email"
				id="email"
				name="email"
				placeholder="Email"
				v-model="form.email"
			/>
			<label for="password">Mot de passe</label>
			<input
				type="password"
				id="password"
				name="password"
				placeholder="Mot de passe"
				v-model="form.password"
			/>
			<button type="submit">Se connecter</button>
		</form>
		<div class="error" v-if="error">{{ error }}</div>
	</div>
</template>

<script setup>
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();

let form = reactive({
	email: "",
	password: "",
});

let error = ref(null);

const login = async () => {
	await axios
		.post("http://localhost:8888/public/php/login.php", form)
		.then((response) => {
			if (response.data.status === "success") {
				localStorage.setItem("token", JSON.stringify(response.data.user));
				localStorage.setItem("id_user", response.data.id_user);
				router.push("/dashboard");
			} else {
				error.value = response.data.message;
			}
		})
		.catch((error) => {
			console.log(error);
		});
};
</script>
