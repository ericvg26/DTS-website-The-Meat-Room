document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');

    const products = [
        {
            "id": 1,
            "name": "Boerewors",
            "price": "$20.00 / 800gm",
            "description": "Traditional South African sausage made with a blend of spices.",
            "image": "assets/imgs/boerewors.webp"
        },
        {
            "id": 2,
            "name": "Hamburger Patties",
            "price": "$15.00 / 4 per pack",
            "description": "Juicy and flavorful hamburger patties made from premium beef.",
            "image": "assets/imgs/patties.webp"
        },
        {
            "id": 3,
            "name": "Sirloin Steak",
            "price": "$17.00 / 400gm",
            "description": "Tender and juicy sirloin steak, perfect for grilling.",
            "image": "assets/imgs/BeefSirloin.webp"
        },
        {
            "id": 4,
            "name": "Scotch Fillet Steak",
            "price": "$26.00 / 500gm",
            "description": "Rich and flavorful scotch fillet steak.",
            "image": "assets/imgs/ScotchFillet.webp"
        },
        {
            "id": 5,
            "name": "Beef Biltong",
            "price": "$59.00 / kg",
            "description": "Traditional South African dried meat snack.",
            "image": "assets/imgs/Biltong.webp"
        },
        {
            "id": 6,
            "name": "Chicken Breast",
            "price": "$10.99 / 500gm",
            "description": "Fresh and tender chicken breast.",
            "image": "assets/imgs/chickenbreast.webp"
        },
        {
            "id": 7,
            "name": "Whole Chicken",
            "price": "$16.00",
            "description": "Whole chicken, perfect for roasting.",
            "image": "assets/imgs/wholechicken.webp"
        },
        {
            "id": 8,
            "name": "Chicken Drumstick",
            "price": "$7.00 / 500gm",
            "description": "Juicy and flavorful chicken drumsticks.",
            "image": "assets/imgs/drumstick.webp"
        },
        {
            "id": 9,
            "name": "Dry Wors",
            "price": "$62.99 / kg",
            "description": "Traditional South African dried sausage.",
            "image": "assets/imgs/dryword.webp"
        },
        {
            "id": 10,
            "name": "Lamb Rack",
            "price": "$26.50 / 500gm",
            "description": "Tender and flavorful lamb rack.",
            "image": "assets/imgs/lambrack.webp"
        },
        {
            "id": 11,
            "name": "Pork Ribs",
            "price": "$20.00 / 1.2kg",
            "description": "Succulent pork ribs, perfect for BBQ.",
            "image": "assets/imgs/ribs.webp"
        },
        {
            "id": 12,
            "name": "Butterfly Lamb Leg",
            "price": "$25.50 / 700gm",
            "description": "Butterflied lamb leg, ready for grilling.",
            "image": "assets/imgs/lambleg.webp"
        }
    ];

    const product = products.find(p => p.id == productId);
    if (product) {
        document.getElementById('product-name').textContent = product.name;
        document.getElementById('product-image').src = product.image;
        document.getElementById('product-image').alt = product.name;
        document.getElementById('product-price').textContent = product.price;
        document.getElementById('product-description').textContent = product.description;
    }
});
