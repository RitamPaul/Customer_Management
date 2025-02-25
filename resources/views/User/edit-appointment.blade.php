<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Appointment</title>
    <link rel="stylesheet" href="{{asset('css/User/add-appointment.css')}}">
</head>
<body>
    <div class="user-info" id="user-info">
        <p class="username" id="username">Name : {{session('cust_name')}}</p>
        <p class="email" id="email">Email : {{session('cust_email')}}</p>
    </div>
    <div class="appointment-container">
        <h1>Edit Your Appointment</h1>
        <form action="{{route('updateAppointment', $details->id)}}" method="POST">
            @csrf
            @method('PUT')
            <div id="appointmentForm" class="appointment-form">
                <h2>Available Services</h2>
                <div class="checkbox-container">
                    <label><input type="checkbox" class="service-checkbox" value="Service 1" data-cost="50"
                            {{in_array('Service 1', $services) ? 'checked' : ''}}> Service 1 (₹ 50)</label>
                    <label><input type="checkbox" class="service-checkbox" value="Service 2" data-cost="40"
                            {{in_array('Service 2', $services) ? 'checked' : ''}}> Service 2 (₹ 40)</label>
                    <label><input type="checkbox" class="service-checkbox" value="Service 3" data-cost="30"
                            {{in_array('Service 3', $services) ? 'checked' : ''}}> Service 3 (₹ 30)</label>
                    <label><input type="checkbox" class="service-checkbox" value="Service 4" data-cost="20"
                            {{in_array('Service 4', $services) ? 'checked' : ''}}> Service 4 (₹ 20)</label>
                </div>

                <table id="selectedServicesTable" class="hidden">
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

            <input type="hidden" name="total_cost" id="totalCostInput">
                
            <div class="input-group">
                <label for="timeShift">Time Shift <span style="color: red;">*</span> </label>
                <select id="timeShift" name="timeShift" required>
                    <option value="">Select Shift</option>
                    <option value="morning" {{$details->time_shift=='morning' ? 'selected' : ''}}>Morning</option>
                    <option value="afternoon" {{$details->time_shift=='afternoon' ? 'selected' : ''}}>Afternoon</option>
                    <option value="evening" {{$details->time_shift=='evening' ? 'selected' : ''}}>Evening</option>
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
            <button type="submit">Update</button>
        </form>
    </div>

    <script>
        function changeInCheckoutTable() {
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
            document.getElementById('totalCostInput').value = totalCost;

            let table = document.getElementById('selectedServicesTable');

            if (selectedServices.length > 0)
                table.classList.remove('hidden');
            else {
                table.classList.add('hidden');
                alert("If no service is selected, no updation in service will be done");
            }
        }
        // for PRE SELECTED Checkbox
        document.querySelectorAll('.service-checkbox').forEach(checkbox => {
            changeInCheckoutTable();
            // for NEW SELECTED Checkbox
            checkbox.addEventListener('change', function() {
                changeInCheckoutTable();
            });
        });

        const timeShift = document.getElementById("timeShift");
        const timeDuration = document.getElementById("timeDuration");

        const durations = {
            morning: ["9:00 AM - 10:00 AM", "10:00 AM - 11:00 AM", "11:00 AM - 12:00 PM"],
            afternoon: ["1:00 PM - 1:40 PM", "2:00 PM - 2:40 PM", "3:00 PM - 3:40 PM"],
            evening: ["5:00 PM - 5:25 PM", "5:30 PM - 5:55 PM", "6:00 PM - 6:25 PM"]
        };
        // for PRE SELECTED Timings
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
        // for NEW SELECTED Timings
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
    </script>
</body>
</html>
