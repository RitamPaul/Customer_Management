<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="{{asset('css/User/dashboard.css')}}">
</head>
<body>
    <div class="user-info" id="user-info">
        <p class="username" id="username">Name : {{session('cust_name')}}</p>
        <p class="email" id="email">Email : {{session('cust_email')}}</p>
    </div>
    <div class="dashboard-container">
        <h1 class="dashboard-title">Welcome to Dashboard</h1>
        <div class="header">
            <button type="button" class="btn add-btn" id="openFormBtn">Add Appointment</button>
            {{-- <!-- <a href="/add-appointment-user" class="btn add-btn">Add Appointment</a> --> --}}
            <a href="{{route('userLogout')}}" class="btn logout-btn">Logout</a>
        </div>

        <!-- Appointment Form -->
        <div id="appointmentForm" class="appointment-form hidden">
            <h2>Available Services</h2>
            <form id="serviceForm" action="/add-appointment" method="POST">
                @csrf
                <div class="checkbox-container">
                    <label><input type="checkbox" class="service-checkbox" value="Service 1" data-cost="50"> Service 1 (₹ 50)</label>
                    <label><input type="checkbox" class="service-checkbox" value="Service 2" data-cost="40"> Service 2 (₹ 40)</label>
                    <label><input type="checkbox" class="service-checkbox" value="Service 3" data-cost="30"> Service 3 (₹ 30)</label>
                    <label><input type="checkbox" class="service-checkbox" value="Service 4" data-cost="20"> Service 4 (₹ 20)</label>
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

                <input type="hidden" name="total_cost" id="totalCostInput">
                <button type="submit" class="btn select-time-btn hidden" id="submitBtn">Select Timings</button>
            </form>
        </div>

        @if (!isset($details))
            <h1 class="appointments-heading">No Appointments Booked Till Now</h1>
        @else
            <h1 class="appointments-heading">Your All Appointments Till Now</h1>
            <div class="filter-container">
                <label for="dateFilter">Filter by Date:</label>
                <input type="date" id="dateFilter" name="filterDate">
            </div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Services</th>
                        <th>Total Cost</th>
                        <th>Time Shift</th>
                        <th>Time Duration</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="appointment-list">
                    @foreach ($details as $rownum => $rowdata)
                        <tr>
                            <td>{{$loop->index + 1}}</td>
                            <td>{{\Carbon\Carbon::parse($rowdata->created_at)->format('Y-m-d')}}</td>
                            <td>{{$rowdata->services}}</td>
                            <td>₹ {{$rowdata->cost}}</td>
                            <td>{{$rowdata->time_shift}}</td>
                            <td>{{$rowdata->time_duration}}</td>
                            <td>
                                <a href="{{route('viewAppointment', $rowdata->id)}}" class="btn view-btn">View</a>
                                <a href="{{route('editAppointment', $rowdata->id)}}" class="btn edit-btn">Edit</a>
                                <a href="{{route('deleteAppointment', $rowdata->id)}}" class="btn delete-btn">Delete</a>
                            </td>
                        </tr>
                    @endforeach                    
                </tbody>
            </table>
        @endif        
    </div>

    <script>
        document.getElementById('openFormBtn').addEventListener('click', function() {
            document.getElementById('appointmentForm').classList.toggle('hidden');
        });

        document.querySelectorAll('.service-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
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
                let submitBtn = document.getElementById('submitBtn');

                if (selectedServices.length > 0) {
                    table.classList.remove('hidden');
                    submitBtn.classList.remove('hidden');
                } else {
                    table.classList.add('hidden');
                    submitBtn.classList.add('hidden');
                }
            });
        });

        // document.getElementById('generateTableBtn').addEventListener('click', function() {
        //     let checkboxes = document.querySelectorAll('.service-checkbox:checked');
        //     let tableBody = document.querySelector('#selectedServicesTable tbody');
        //     tableBody.innerHTML = ''; // Clear previous selections

        //     checkboxes.forEach(checkbox => {
        //         let row = document.createElement('tr');
        //         row.innerHTML = `
        //             <td>${checkbox.dataset.name}</td>
        //             <td>₹ ${checkbox.dataset.cost}</td>
        //             <td><button type="button" class="btn remove-btn">Remove</button></td>
        //         `;
        //         tableBody.appendChild(row);
        //     });

        //     document.getElementById('selectedServicesContainer').classList.remove('hidden');

        //     // Add remove event to newly created remove buttons
        //     document.querySelectorAll('.remove-btn').forEach(button => {
        //         button.addEventListener('click', function() {
        //             this.closest('tr').remove();
        //             if (tableBody.children.length === 0) {
        //                 document.getElementById('selectedServicesContainer').classList.add('hidden');
        //             }
        //         });
        //     });
        // });


        // Date Filter Workings
        dateFilter = document.getElementById('dateFilter');
        dateFilter.addEventListener('input', function(){
            tbody = document.getElementById('appointment-list');
            trs = tbody.querySelectorAll('tr');
            for(let i=0; i<trs.length; ++i){
                const tableDate = trs[i].querySelectorAll('td')[1];
                // console.log(tableDate[1].textContent, dateFilter.value, tableDate[1].textContent==dateFilter.value);
                if(!dateFilter.value)
                    trs[i].style.display = 'table-row';
                else{
                    if(tableDate.textContent != dateFilter.value)
                        trs[i].style.display = 'none';
                    else
                        trs[i].style.display = 'table-row';
                }
            }
        });
    </script>
</body>
</html>
