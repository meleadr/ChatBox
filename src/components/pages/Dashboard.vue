<template>
	<div>
		<h1>Select users to chat with</h1>
		<select id="userList" multiple>
			<option v-for="user in users" :key="user.id_user" :value="user.id_user">
				{{ user.mail }}
			</option>
		</select>
		<button id="startChat" @click="startChat">Start Chat</button>
	</div>
</template>

<script setup>
import axios from "axios";
import { ref } from "vue";
import { useRouter } from "vue-router";

const users = ref([]);

const router = useRouter();

const getUsers = async () => {
	const email = localStorage.getItem("token");
	await axios
		.get("http://localhost:8888/public/php/userlist.php?email=" + email)
		.then((response) => {
			console.log(response.data.users);
			users.value = response.data.users;
		})
		.catch((error) => {
			console.log(error);
		});
};

const startChat = async () => {
	const selectedUsers = document.getElementById("userList").selectedOptions;
	const selectedUsersId = [];
	for (let i = 0; i < selectedUsers.length; i++) {
		selectedUsersId.push(selectedUsers[i].value);
	}
	selectedUsersId.push(localStorage.getItem("id_user"));
	await axios
		.post("http://localhost:8888/public/php/chatroom.php", {
			name: "Chatroom avec " + selectedUsersId.join(", "),
			users: selectedUsersId,
		})
		.then((response) => {
			if (response.data.status === "success") {
				router.push(`/chatroom/${response.data.chatroomId}`);
			} else {
				console.log(response.data.message);
			}
		})
		.catch((error) => {
			console.log(error);
		});
};

getUsers();
</script>

<style scoped></style>
