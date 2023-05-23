<template>
	<div>
		<h1>Chatroom {{ chatroomId }}</h1>
		<div class="chatbox">
			<div v-for="message in messages" :key="message.id">
				{{ message.id_user }}: {{ message.content }}
			</div>
			<input
				type="text"
				v-model="newMessage"
				placeholder="Type your message here..."
			/>
			<button @click="sendMessage">Send</button>
		</div>
	</div>
</template>

<script setup>
import axios from "axios";
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";

const route = useRoute();
const chatroomId = route.params.id;
const messages = ref([]);
const newMessage = ref("");

const getMessages = async () => {
	await axios
		.get(
			`http://localhost:8888/public/php/messages.php?chatroomId=${chatroomId}`
		)
		.then((response) => {
			if (response.data.status === "success") {
				messages.value = response.data.messages;
			} else {
				console.log(response.data.message);
			}
		})
		.catch((error) => {
			console.log(error);
		});
};

const sendMessage = async () => {
	await axios
		.post("http://localhost:8888/public/php/sendmessage.php", {
			chatroomId: chatroomId,
			text: newMessage.value,
			sender: localStorage.getItem("id_user"),
		})
		.then((response) => {
			console.log(response.data);
			if (response.data.status === "success") {
				newMessage.value = "";
				getMessages();
			} else {
				console.log(response.data.message);
			}
		})
		.catch((error) => {
			console.log(error);
		});
};

onMounted(getMessages);
</script>

<style scoped>
.chatbox {
	display: flex;
	flex-direction: column;
	gap: 1em;
}
</style>
