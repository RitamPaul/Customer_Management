<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointment</title>
    <link rel="stylesheet" href="{{asset('css/User/add-appointment.css')}}">
</head>
<body>
    <div class="user-info" id="user-info">
        <p class="username" id="username">Name : {{session('cust_name')}}</p>
        <p class="email" id="email">Email : {{session('cust_email')}}</p>
    </div>
    <div class="appointment-container">
        <h1>View Your Appointment</h1>
        <div id="appointmentForm" class="appointment-form">
            <h2>Available Services</h2>
            <div class="checkbox-container">
                <label><input type="checkbox" class="service-checkbox" value="Service 1" data-cost="50"
                        {{in_array('Service 1', $services) ? 'checked' : ''}} disabled> Service 1 (₹ 50)</label>
                <label><input type="checkbox" class="service-checkbox" value="Service 2" data-cost="40"
                        {{in_array('Service 2', $services) ? 'checked' : ''}} disabled> Service 2 (₹ 40)</label>
                <label><input type="checkbox" class="service-checkbox" value="Service 3" data-cost="30"
                        {{in_array('Service 3', $services) ? 'checked' : ''}} disabled> Service 3 (₹ 30)</label>
                <label><input type="checkbox" class="service-checkbox" value="Service 4" data-cost="20"
                        {{in_array('Service 4', $services) ? 'checked' : ''}} disabled> Service 4 (₹ 20)</label>
            </div>

            <table id="selectedServicesTable">
                <caption><h2>Checkout List Summary</h2></caption>
                <thead>
                    <tr>
                        <th>Service Name</th>
                        <th>Cost</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <td><strong>Total Cost</strong></td>
                        <td><strong>₹ <span id="totalCost">0</span></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="input-group">
            <label for="timeShift">Time Shift:</label>
            <select id="timeShift" name="timeShift" required>
                <option value="">Select Shift</option>
                <option value="morning" {{$details->time_shift=='morning' ? 'selected' : ''}}>Morning</option>
                <option value="afternoon" {{$details->time_shift=='afternoon' ? 'selected' : ''}}>Afternoon</option>
                <option value="evening" {{$details->time_shift=='evening' ? 'selected' : ''}}>Evening</option>
            </select>
        </div>
        <div class="input-group">
            <label for="timeDuration">Time Duration:</label>
            <select id="timeDuration" name="timeDuration" required>
                <option value="">Select Time Shift at first</option>
            </select>
        </div>
    </div>

    <script>
        document.querySelectorAll('.service-checkbox').forEach(checkbox => {
            let selectedServices = document.querySelectorAll('.service-checkbox:checked');
            let tableBody = document.querySelector('#selectedServicesTable tbody');
            let totalCost = 0;

            tableBody.innerHTML = '';

            selectedServices.forEach(service => {
                let row = document.createElement('tr');
                row.innerHTML = `
                    <td><input type="hidden" name="selected_services[]" value="${service.value}">${service.value}</td>
                    <td><input type="hidden" name="costs[]" value="${service.dataset.cost}">₹ ${service.dataset.cost}</td>
                `;
                tableBody.appendChild(row);
                totalCost += parseInt(service.dataset.cost);
            });

            document.getElementById('totalCost').textContent = totalCost;
        });

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
            timeDuration.value = "{{$details->time_duration}}";
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
                if(this.value == "{{$details->time_shift}}")
                    timeDuration.value = "{{$details->time_duration}}";
            }
        });

        // returnBack = document.getElementById('returnBack');
        // returnBack.addEventListener('click', function(){
        //     console.warn('tried to return back');
        //     window.history.back();
        // });
    </script>
</body>
</html>
