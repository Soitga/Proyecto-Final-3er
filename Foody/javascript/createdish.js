document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.create-dish-container');
    const ingredientsList = document.getElementById('ingredients-list');
    const addIngredientBtn = document.getElementById('add-ingredient');
    const selectedIngredients = new Set();

    addIngredientBtn.addEventListener('click', function() {
        const ingredientSelect = document.getElementById('ingredients');
        const selectedIngredient = ingredientSelect.value;
        
        if (selectedIngredient && !selectedIngredients.has(selectedIngredient)) {
            selectedIngredients.add(selectedIngredient);
            const li = document.createElement('li');
            li.textContent = selectedIngredient;
            
            const deleteBtn = document.createElement('button');
            deleteBtn.textContent = 'X';
            deleteBtn.className = 'delete-ingredient';
            deleteBtn.onclick = function() {
                ingredientsList.removeChild(li);
                selectedIngredients.delete(selectedIngredient);
            };
            
            li.appendChild(deleteBtn);
            ingredientsList.appendChild(li);
        }
    });

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const dishData = {
            name: document.getElementById('name').value,
            description: document.getElementById('description').value,
            price: parseFloat(document.getElementById('price').value),
            category: document.getElementById('category').value
        };

        try {
            const response = await fetch('save_dish.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(dishData)
            });

            const result = await response.json();
            
            if (result.success) {
                alert(result.message);
                form.reset();
                ingredientsList.innerHTML = '';
                selectedIngredients.clear();
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            alert('Error: ' + error.message);
            console.error('Error:', error);
        }
    });

    const imageInput = document.getElementById('image');
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
            };
            reader.readAsDataURL(file);
        }
    });
});