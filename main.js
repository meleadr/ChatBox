import "./style.css";

const app = document.querySelector("#app");

app.innerHTML = `
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
			app.innerHTML = "";

			// add chatbox HTML structure here
			app.innerHTML = `
		  <div>
			<h1>Chatbox</h1>
			<div class="chatbox">
				<!-- Add chatbox structure here -->
			</div>
		  </div>
		`;
		})
		.catch((error) => {
			// handle the error
			console.error("Error:", error);
			// You might want to display an error message to the user here
		});
});
