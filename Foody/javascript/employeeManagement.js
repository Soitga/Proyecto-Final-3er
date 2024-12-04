document.querySelectorAll('.menu-item').forEach(item => {
    item.addEventListener('click', (e) => {
        e.preventDefault();
        
        document.querySelector('.sidebar').classList.toggle('open');
        document.querySelector('.main-content').classList.toggle('sidebar-open');

        if (e.target.getAttribute('data-section') === 'employees') {
            document.getElementById('employees-section').style.display = 'block';
        }
    });
});

const employees = [
    { id: 1, name: 'John Doe', position: 'Waiter' },
    { id: 2, name: 'Jane Smith', position: 'Chef' },
    { id: 3, name: 'Michael Johnson', position: 'Manager' }
];

const renderEmployeeList = () => {
    const employeeTable = document.querySelector('#employee-list tbody');
    employeeTable.innerHTML = ''; 

    employees.forEach(employee => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${employee.id}</td>
            <td>${employee.name}</td>
            <td>${employee.position}</td>
            <td>
                <button class="button edit-btn">Edit</button>
                <button class="button delete-btn">Delete</button>
            </td>
        `;
        employeeTable.appendChild(row);
    });
};

renderEmployeeList();
