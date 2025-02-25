<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="{{asset('css/User/login-form.css')}}">
</head>
<body>
    <div class="container">
        @if (session()->has('error'))
            <div class="error-message">
                <p>{{session('error')}}</p>
            </div>
        @endif
        <h1>Login Yourself</h1>
        <form action="{{route('verifyUser')}}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Email <span style="color: red;">*</span> </label>
                <input type="email" id="email" name="userEmail" value="{{old('userEmail')}}" placeholder="email format = sometext@sometext.sometext" required>
                <div id="email-error" style="color: red;">
                    {{session()->has('validationMsg') && session('validationMsg')->has('userEmail') ? session('validationMsg')->first('userEmail') : ''}}
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password <span style="color: red;">*</span> </label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="userPassword" value="{{old('userPassword')}}" placeholder="password length of atleast 8 characters" required>
                    <span class="eye-icon" onclick="togglePassword()">👁️</span>
                </div>
                <div id="password-error" style="color: red;">
                    {{session()->has('validationMsg') && session('validationMsg')->has('userPassword') ? session('validationMsg')->first('userPassword') : ''}}
                </div>
            </div>

            <button type="submit">Login</button>
        </form>

        <div class="register-container">
            Don't have an account? <a href="/">REGISTER</a>
        </div>
    </div>

    <script>
        function togglePassword() {
            let passwordField = document.getElementById("password");
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>
</body>
</html>
