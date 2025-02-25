<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Appointment</title>
    <link rel="stylesheet" href="{{asset('css/User/add-appointment.css')}}">
</head>
<body>
    <div class="user-info" id="user-info">
        <p class="username" id="username">Name : {{session('cust_name')}}</p>
        <p class="email" id="email">Email : {{session('cust_email')}}</p>
    </div>
    <div class="appointment-container">
        <h1>Add Appointment</h1>
        <form action="{{route('saveAppointment')}}" method="POST">
            @csrf
            <div class="input-group">
                <label for="timeShift">Time Shift <span style="color: red;">*</span> </label>
                <select id="timeShift" name="timeShift" required>
                    <option value="">Select Shift</option>
                    <option value="morning" {{old('timeShift')=='morning'?'selected':''}}>Morning</option>
                    <option value="afternoon" {{old('timeShift')=='afternoon'?'selected':''}}>Afternoon</option>
                    <option value="evening" {{old('timeShift')=='evening'?'selected':''}}>Evening</option>
                </select>
                <div id="time-shift-error" style="color: red;">
                    {{session()->has('validationMsg') && session('validationMsg')->has('timeShift') ? session('validationMsg')->first('timeShift') : ''}}
                </div>
            </div>
            <div class="input-group">
                <label for="timeDuration">Time Duration <span style="color: red;">*</span> </label>
                <select id="timeDuration" name="timeDuration" required>
                    <option value="">Select Time Shift at first</option>
                </select>
                <div id="time-duration-error" style="color: red;">
                    {{session()->has('validationMsg') && session('validationMsg')->has('timeDuration') ? session('validationMsg')->first('timeDuration') : ''}}
                </div>
            </div>
            <button type="submit">Submit</button>
        </form>
    </div>

    <script>
        const timeShift = document.getElementById("timeShift");
        const timeDuration = document.getElementById("timeDuration");

        const durations = {
            morning: ["9:00 AM - 10:00 AM", "10:00 AM - 11:00 AM", "11:00 AM - 12:00 PM"],
            afternoon: ["1:00 PM - 1:40 PM", "2:00 PM - 2:40 PM", "3:00 PM - 3:40 PM"],
            evening: ["5:00 PM - 5:25 PM", "5:30 PM - 5:55 PM", "6:00 PM - 6:25 PM"]
        };

        if(timeShift.value in durations){
            timeDuration.innerHTML = '<option value="">Select Duration</option>';
            durations[timeShift.value].forEach(duration => {
                let option = document.createElement("option");
                option.value = duration;
                option.textContent = duration;
                timeDuration.appendChild(option);
            });
        }

        timeShift.addEventListener("change", function() {
            timeDuration.innerHTML = '<option value="">Select Duration</option>';
            if (durations[this.value]) {
                durations[this.value].forEach(duration => {
                    let option = document.createElement("option");
                    option.value = duration;
                    option.textContent = duration;
                    timeDuration.appendChild(option);
                });
            }
        });
    </script>
</body>
</html>
