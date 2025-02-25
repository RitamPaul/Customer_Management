<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="{{asset('css/User/register-form.css')}}">
</head>
<body>
    <div class="container">
        @if (session()->has('success'))
            <div class="success-message">
                <p>{{session('success')}}</p>
            </div>
        @elseif (session()->has('error'))
            <div class="error-message">
                <p>{{session('error')}}</p>
            </div>
        @endif
        <h1>Register Yourself</h1>
        <form action="{{route('saveAddUser')}}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Email <span style="color: red;">*</span> </label>
                <input type="email" id="email" name="userEmail" value="{{old('userEmail')}}" placeholder="email format = sometext@sometext.sometext" required>
                <div id="email-error" style="color: red;">
                    {{session()->has('validationMsg') && session('validationMsg')->has('userEmail') ? session('validationMsg')->first('userEmail') : ''}}
                </div>
            </div>

            <div class="form-group">
                <label for="first-name">First Name <span style="color: red;">*</span> </label>
                <input type="text" id="first-name" name="userFirstName" value="{{old('userFirstName')}}" placeholder="enter your first name" oninput="toggleMiddleName()" required>
                <div id="first-name-error" style="color: red;">
                    {{session()->has('validationMsg') && session('validationMsg')->has('userFirstName') ? session('validationMsg')->first('userFirstName') : ''}}
                </div>
            </div>

            <div class="form-group hidden" id="middle-name-group">
                <label for="middle-name">Middle Name (Optional) </label>
                <input type="text" id="middle-name" name="userMiddleName" value="{{old('userMiddleName')}}" placeholder="enter your middle name">
            </div>

            <div class="form-group">
                <label for="last-name">Last Name <span style="color: red;">*</span> </label>
                <input type="text" id="last-name" name="userLastName" value="{{old('userLastName')}}" placeholder="enter your last name" required>
                <div id="last-name-error" style="color: red;">
                    {{session()->has('validationMsg') && session('validationMsg')->has('userLastName') ? session('validationMsg')->first('userLastName') : ''}}
                </div>
            </div>

            <div class="form-group">
                <label for="contact">Contact Number <span style="color: red;">*</span> </label>
                <input type="tel" id="contact" name="userContact" value="{{old('userContact')}}" placeholder="contact number must be in 10 digits" required>
                <div id="contact-error" style="color: red;">
                    {{session()->has('validationMsg') && session('validationMsg')->has('userContact') ? session('validationMsg')->first('userContact') : ''}}
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

            <button type="submit">Register</button>
        </form>

        <div class="login-container">
            Already registered? <a href="/login-user">LOGIN</a>
        </div>
    </div>

    <script>
        const middleNameGroup = document.getElementById('middle-name-group');
        const oldMiddleName = "{{old('userMiddleName')}}";
        if(oldMiddleName)
            middleNameGroup.style.display = 'block';
        function toggleMiddleName() {
            let firstName = document.getElementById("first-name").value;
            let middleNameGroup = document.getElementById("middle-name-group");

            if (firstName.length > 0) {
                middleNameGroup.classList.remove("hidden");
            } else {
                middleNameGroup.classList.add("hidden");
            }
        }
        
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