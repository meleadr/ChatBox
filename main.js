import "./style.css";

document.querySelector("#app").innerHTML = `
  <div>
    <h1>Connexion</h1>
	<div class="card">
		<form id="login-form">
			<label for="email">Email</label>
			<input type="email" id="email" name="email" placeholder="Email">
			<label for="password">Mot de passe</label>
			<input type="password" id="password" name="password" placeholder="Mot de passe">
			<button type="submit">Se connecter</button>
		</form>
	</div>
</div>
`;

const form = document.querySelector("#login-form");
form.addEventListener("submit", (event) => {
	event.preventDefault();
	const formData = new FormData(form);
	const data = Object.fromEntries(formData);
	fetch("http://localhost:8888/api.php", {
		method: "POST",
		body: JSON.stringify(data),
		headers: {
			"Content-Type": "application/json",
		},
	})
		.then((response) => {
			if (!response.ok) {
				throw new Error("Network response was not ok");
			}
			return response.json();
		})
		.then((data) => {
			// handle the response data
			console.log(data);

			// clear everything within #app
			const app = document.querySelector("#app");
			app.innerHTML = "";

			// The part where we create the user list now looks like this
			fetch("http://localhost:8888/userlist.php")
				.then((response) => response.json())
				.then((userList) => {
					let userOptions = userList
						.map((user) => `<option value="${user}">${user}</option>`)
						.join("\n");
					app.innerHTML = `
		  <div>
			<h1>Select users to chat with</h1>
			<select id="userList" multiple>
				${userOptions}
			</select>
			<button id="startChat">Start Chat</button>
		  </div>
		`;

					// Add event listener to the start chat button
					document
						.querySelector("#startChat")
						.addEventListener("click", function () {
							let selectedUsers = Array.from(
								document.querySelector("#userList").selectedOptions
							).map((option) => option.value);
							app.innerHTML = `
			  <div>
				<h1>Chat with ${selectedUsers.join(", ")}</h1>
				<div class="chatbox">
					<!-- Add chatbox structure here -->
					<div id="messageContainer"></div>
					<input type="text" id="messageInput">
					<button id="sendMessage">Send</button>
				</div>
			  </div>
			`;
							// Add logic to handle chat messages here
						});
				});
		})
		.catch((error) => {
			// handle the error
			console.error("Error:", error);
			// You might want to display an error message to the user here
		});
});

const sendMessage = (message) => {
	fetch("http://localhost:8888/sendmessage.php", {
		method: "POST",
		body: JSON.stringify(message),
		headers: {
			"Content-Type": "application/json",
		},
	})
		.then((response) => {
			if (!response.ok) {
				throw new Error("Network response was not ok");
			}
			return response.json();
		})
		.then((data) => {
			// handle the response data
			console.log(data);
		})
		.catch((error) => {
			// handle the error
			console.error("Error:", error);
			// You might want to display an error message to the user here
		});
};

document.querySelector("#sendMessage").addEventListener("click", () => {
	const message = document.querySelector("#messageInput").value;
	sendMessage(message);
});

const getMessages = () => {
	fetch("http://localhost:8888/messages.php")
		.then((response) => response.json())
		.then((messages) => {
			let messageList = messages
				.map((message) => `<div>${message}</div>`)
				.join("\n");
			const messageContainer = document.querySelector("#messageContainer");
			if (messageContainer) {
				messageContainer.innerHTML = messageList;
			}
		});
};

setInterval(() => {
	if (document.querySelector("#messageContainer")) {
		getMessages();
	}
}, 1000);
