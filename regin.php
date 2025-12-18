<?php
	session_start();
	include("./settings/connect_datebase.php");
	
	if (isset($_SESSION['user'])) {
		if($_SESSION['user'] != -1) {
			
			$user_query = $mysqli->query("SELECT * FROM `users` WHERE `id` = ".$_SESSION['user']);
			while($user_read = $user_query->fetch_row()) {
				if($user_read[3] == 0) header("Location: user.php");
				else if($user_read[3] == 1) header("Location: admin.php");
			}
		}
 	}
?>
<html>
	<head> 
		<meta charset="utf-8">
		<title> Регистрация </title>
		
		<script src="https://code.jquery.com/jquery-1.8.3.js"></script>
		<link rel="stylesheet" href="style.css">
	</head>
	<body>
		<div class="top-menu">
			<a href=#><img src = "img/logo1.png"/></a>
			<div class="name">
				<a href="index.php">
					<div class="subname">БЗОПАСНОСТЬ  ВЕБ-ПРИЛОЖЕНИЙ</div>
					Пермский авиационный техникум им. А. Д. Швецова
				</a>
			</div>
		</div>
		<div class="space"> </div>
		<div class="main">
			<div class="content">
				<div class = "login">
					<div class="name">Регистрация</div>
				
					<div class = "sub-name">Логин:</div>
					<input name="_login" type="text" placeholder="" onkeypress="return PressToEnter(event)"/>
					<div class = "sub-name">Пароль:</div>
					<input name="_password" type="password" placeholder="" onkeypress="return PressToEnter(event)"/>
					<div class = "sub-name">Повторите пароль:</div>
					<input name="_passwordCopy" type="password" placeholder="" onkeypress="return PressToEnter(event)"/>
					<div class = "sub-name">Email:</div>
					<input name="_email" type="email" placeholder="user@example.com" onkeypress="return PressToEnter(event)"/>
					
					<a href="login.php">Вернуться</a>
					<input type="button" class="button" value="Зайти" onclick="RegIn()" style="margin-top: 0px;"/>
					<img src = "img/loading.gif" class="loading" style="margin-top: 0px;"/>
				</div>
				
				<div class="footer">
					© КГАПОУ "Авиатехникум", 2020
					<a href=#>Конфиденциальность</a>
					<a href=#>Условия</a>
				</div>
			</div>
		</div>
		
		<script>
			var loading = document.getElementsByClassName("loading")[0];
			var button = document.getElementsByClassName("button")[0];
			
			function RegIn() {
				var _login = document.getElementsByName("_login")[0].value;
				var _password = document.getElementsByName("_password")[0].value;
				var _passwordCopy = document.getElementsByName("_passwordCopy")[0].value;
				var _email = document.getElementsByName("_email")[0].value;
				
				// Проверка email
				if (!_email.includes('@') || !_email.includes('.')) {
					alert("Введите корректный email");
					return;
				}
				
				// Проверка пароля
				var passwordErrors = validatePassword(_password);
				if (passwordErrors.length > 0) {
					alert("Ошибки в пароле:\n" + passwordErrors.join("\n"));
					return;
				}
				
				if(_password != _passwordCopy) {
					alert("Пароли не совпадают");
					return;
				}
				
				loading.style.display = "block";
				button.className = "button_diactive";
				
				var data = new FormData();
				data.append("login", _login);
				data.append("password", _password);
				data.append("email", _email);
							// AJAX запрос
							$.ajax({
								url         : 'ajax/regin_user.php',
								type        : 'POST', // важно!
								data        : data,
								cache       : false,
								dataType    : 'html',
								// отключаем обработку передаваемых данных, пусть передаются как есть
								processData : false,
								// отключаем установку заголовка типа запроса. Так jQuery скажет серверу что это строковой запрос
								contentType : false, 
								// функция успешного ответа сервера
								success: function (_data) {
									console.log("Ответ сервера: " + _data);
									
									if(_data.startsWith("password_error:")) {
										alert("Ошибка пароля: " + _data.split(":")[1]);
										loading.style.display = "none";
										button.className = "button";
									} else if(_data.startsWith("email_error:")) {
										alert("Ошибка email: " + _data.split(":")[1]);
										loading.style.display = "none";
										button.className = "button";
									} else if(_data == "-1") {
										alert("Пользователь с таким логином или email уже существует.");
										loading.style.display = "none";
										button.className = "button";
									} else {
										location.reload();
										loading.style.display = "none";
										button.className = "button";
									}
								},
								// функция ошибки
								error: function( ){
									console.log('Системная ошибка!');
									loading.style.display = "none";
									button.className = "button";
								}
							});
			}
			function PressToEnter(e) {
				if (e.keyCode == 13) {
					var _login = document.getElementsByName("_login")[0].value;
					var _password = document.getElementsByName("_password")[0].value;
					var _passwordCopy = document.getElementsByName("_passwordCopy")[0].value;
					
					if(_password != "") {
						if(_login != "") {
							if(_passwordCopy != "") {
								RegIn();
							}
						}
					}
				}
			}
			// Функции для проверки пароля
			function validatePassword(password) {
				var errors = [];
				
				if (password.length < 8) {
					errors.push("Пароль должен содержать минимум 8 символов");
				}
				if (!/[A-Z]/.test(password)) {
					errors.push("Пароль должен содержать хотя бы одну заглавную букву");
				}
				if (!/[a-z]/.test(password)) {
					errors.push("Пароль должен содержать хотя бы одну строчную латинскую букву");
				}
				if (!/[0-9]/.test(password)) {
					errors.push("Пароль должен содержать хотя бы одну цифру");
				}
				if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
					errors.push("Пароль должен содержать хотя бы один специальный символ (!@#$%^&* и т.д.)");
				}
				
				return errors;
			}
			
		</script>
	</body>
</html>