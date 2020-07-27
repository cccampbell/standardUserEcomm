let choose_payment_section = document.getElementById('choose_payment_section');
let add_payment_section = document.getElementById('add_payment_section');
let chosen_payment_info = document.getElementById('chosen_payment');

// payent_option_buttons
let add_card_button = document.getElementById('add_card_button');
let change_payment_method = document.getElementById('change_payment_method');

let cancel_card = document.getElementById('cancel_card');

let checkout_price = parseFloat(document.getElementById('checkout_price').innerHTML).toFixed(2);

console.log(checkout_price);

let postage_options = document.getElementById('postage_dropdown');

let selected_postage;

let postage = postage_options.options[postage_options.selectedIndex].text;

let postage_price = parseFloat(postage.match(/(?!:\s£)(\d+[\\.]\d+)/g)[0]).toFixed(2);

let products_total = parseFloat(checkout_price.innerText);

// calculateTotal(getCheckoutTotal());

// changePostage(selected_postage);

// hardcode delivery into 

// debug - set default country so postage is up on basket asap
//          could you find via header which country
//              if so and user is in country not able to deliver, flash message in website saying so
//          if user logged in would be easier


window.onload = function () {

    // debug - do this before page loads
    // check if user logged in
    let logged_in = checkLoggedIn();

    if(!logged_in) {

        // if none remove change payment button
        change_payment_method.remove();
        // and when click add card both add card and cancel buttons are not there as not needed to store
        document.getElementById('add_card').remove();
        cancel_card.remove();

    }
    
    
    
    // hide both sections
    if (!choose_payment_section.classList.contains('hide_payment_option') || !add_payment_section.classList.contains('hide_payment_option')) {
        choose_payment_section.classList.add('hide_payment_option');
        add_payment_section.classList.add('hide_payment_option');
    }

    if (chosen_payment_info.innerHTML === '') {

        chosen_payment_info.innerHTML = 'please add a payment method';

    }

    // onclick be put down to one, find which one clicked, put in id as param to togglePaymentSection

    add_card_button.onclick = function () {

        togglePaymentSection('add_payment_section');

    };

    change_payment_method.onclick = function () {

        togglePaymentSection('choose_payment_section');

    };



    cancel_card.onclick = function () {

        closeSection('add_payment_section');

    }


    choose_payment_section.querySelectorAll('button').forEach($el => {

        $el.onclick = function () {

            // get value from button
            // put into chosen payment section
            // show user selcted payment in chosen payment section

            // close off section
            closeSection('choose_payment_section');

        }

    });

    document.getElementById('choose_country_dropdown').addEventListener('change', (el) => {



        let dropdown = document.getElementById('choose_country_dropdown');

        let selected = dropdown.options[dropdown.selectedIndex];

        getCountryDeliveryServices(selected.value);

        // console.log(selected.value);

    });

    document.getElementById('postage_dropdown').addEventListener('change', (el) => {
        // 
        getPostage(postage_options.options[postage_options.selectedIndex]);

    });
    
    

}

// Create a Stripe client.
var stripe = Stripe('pk_test_51Gu0wUDIohyHdIVWTWOhBpA9W8w8C9UaTwgRrHmxRbj43bbjpfvDVSPl96TBS6ijSgP5WdPCCOxdrPMBGXkzNYu800CL3t39Og');

// Create an instance of Elements.
var elements = stripe.elements();

// Custom styling can be passed to options when creating an Element.
// (Note that this demo uses a wider set of styles than the guide below.)
var style = {
    base: {
        color: '#32325d',
        fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
        fontSmoothing: 'antialiased',
        fontSize: '16px',
        '::placeholder': {
            color: '#aab7c4'
        }
    },
    invalid: {
        color: '#fa755a',
        iconColor: '#fa755a'
    }
};

// Create an instance of the card Element.
var card = elements.create('card', { style: style });

// Add an instance of the card Element into the `card-element` <div>.
card.mount('#card-element');

// Handle real-time validation errors from the card Element.
card.on('change', function (event) {
    var displayError = document.getElementById('card-errors');
    if (event.error) {
        displayError.textContent = event.error.message;
    } else {
        displayError.textContent = '';
    }
});

// Handle form submission.
var form = document.getElementById('checkout_order_form');

form.addEventListener('submit', function (event) {
    event.preventDefault();

    stripe.createToken(card).then(function (result) {
        if (result.error) {
            // Inform the user if there was an error.
            var errorElement = document.getElementById('card-errors');
            errorElement.textContent = result.error.message;
        } else {
            // Send the token to your server.
            stripeTokenHandler(result.token);
        }
    });
});

// Submit the form with the token ID.
function stripeTokenHandler(token) {
    // Insert the token ID into the form so it gets submitted to the server
    var form = document.getElementById('checkout_order_form');
    var hiddenInput = document.createElement('input');
    hiddenInput.setAttribute('type', 'hidden');
    hiddenInput.setAttribute('name', 'stripeToken');
    hiddenInput.setAttribute('value', token.id);
    form.appendChild(hiddenInput);

    // Submit the form
    form.submit();
}

// show section depending on which clicked
function togglePaymentSection($type) {

    // disable payment option buttons
    toggleDisablePaymentOptionButtons();

    // show selsected section
    document.getElementById($type).classList.toggle('hide_payment_option');
    document.getElementById($type).classList.toggle('show_payment_option');


}

function closeSection($type) {

    document.getElementById($type).classList.toggle('show_payment_option');
    document.getElementById($type).classList.toggle('hide_payment_option');

    toggleDisablePaymentOptionButtons();

}

function toggleDisablePaymentOptionButtons() {

    document.getElementById('payment_option_buttons').querySelectorAll('button').forEach(el => {

        if(el.disabled === false) {
            el.disabled = true;
        } else {
            el.disabled = false;
        }
        
    })

}

// check header for auth token
function checkLoggedIn() {

    let logged_in = false;

    var req = new XMLHttpRequest();
    req.open('GET', document.location, false);
    req.send(null);

    let headers = req.getAllResponseHeaders().toLowerCase();
    headers = headers.split("\n");

    headers.forEach(el => {

        el = el.split(": ");
        if (el[0] === 'authorization') {
            logged_in = true;
        }
    });


    return logged_in;

}

function goToPayment() {

    let payment_section = document.getElementById('user_payment_section');
    let goToBtn = document.getElementById('go_to_payment_btn');

    // show payment section
    payment_section.style.display = 'block';

    // scroll down to payment section
    window.scrollTo({
        top: payment_section.scrollHeight + 50,
        left: 0,
        behavior: 'smooth'
    });

    disableBtn(goToBtn);

}

function getPostage(el) {

    // get value, price and text from selected
    let postage_value = el.value;
    let postage_price = getPriceWithRegex(el.text);

    //  -> check against db, return info
    let xhr = new XMLHttpRequest();

    xhr.open("GET", `/basket/checkout/get_postage_info/${postage_value}/${postage_price}`, true);
    // xhr.open("GET", "/basket/checkout/get_postage_info/" + postage_value + "/" + postage_price, true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.onload = () => {

        if (xhr.status === 200 && xhr.readyState === 4) {

            // put on checkout table

            let updated_postage = JSON.parse(xhr.response);

            changePostage(updated_postage);

            // postage_price = parseFloat(updated_postage).toFixed(2);

        } else {

            alert('Request failed.  Returned status of ' + xhr.status);
        }


    }

    xhr.send();


    
    // let postage_desc = document.getElementById('postage_desc');

    // // take away original postage
    // checkout_price = checkout_price - postage;

    // postage_desc.innerHTML = el.text;

    // console.log(el.text);

    // postage_price.innerHTML = el.value;

    // postage = parseFloat(el.value).toFixed(2);

    

}

function changePostage(json) {

    postage_desc.innerHTML = json.name;

    document.getElementById('postage_price').innerHTML = parseFloat(json.price).toFixed(2);

    changeCheckoutTotal(checkout_price, json.price);

}

function getPriceWithRegex(value) {

    // console.log(value.match(/(?!:\s£)(\d+.*)/g));
    return parseFloat(value.match(/(?!:\s£)(\d+.*)/g)).toFixed(2);

}

function getCheckoutTotal() {

    console.log(postage_price);
    console.log(checkout_price);

    return parseFloat(postage_price) + parseFloat(checkout_price);


}

function changeCheckoutTotal(total, postage_total) {

    // remove old postage amount
    total -= postage_price;
    console.log('total :' + total);
    
    // change variable amount
    postage_price = postage_total;

    // add new postage to total
    total += parseFloat(postage_price);

    checkout_price = total;

    document.getElementById('checkout_price').innerHTML = parseFloat(total).toFixed(2);

}

function calculateTotal(total) {

    checkout_price.innerHTML = total.toFixed(2);

}

const post = (url) =>  {

    return new Promise(function (resolve, reject) {

        let req = new XMLHttpRequest();

        req.open('POST', url, true);
        req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        req.onload = function () {

            if (req.status == 200 && req.readyState === 4) {

                resolve(req.response);

            }
            else {
                reject(Error(req.statusText));
            }

        };

        req.onerror = function () {
            reject(Error("Network Error"));
        }

        req.send();

    });

}

function order() {

    // post through to end point
    post("/basket/checkout")
    .then(data)
    // while loading pull up loading 

    // unsuccessful should be handled by php

    // successful should remove all checkout DOM and slide up the order confirmation

}

function getCountryDeliveryServices(id) {

    let xhr = new XMLHttpRequest();

    // let dropdown = document.getElementById('choose_country_dropdown');


    xhr.open("GET", `/basket/checkout/get_delivery_services/${id}`, true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.onload = () => {


        if (xhr.status === 200 && xhr.readyState === 4) {

            // console.log(xhr.response);

            // [0] => [
            //      'id'
            //      'name'
            //      'price'
            //      'country_id'
            // ] 
            let data = JSON.parse(xhr.response);

            setTimeout(() => {

                populateDropdown(document.getElementById('postage_dropdown'), data);

            }, 500);

            // location.reload();
        } else {
            alert('Request failed.  Returned status of ' + xhr.status);
        }
    };

    xhr.send();
    return false;

}

function populateDropdown(dropdown, data) {

    // console.log(data);
    let dropdown_length = dropdown.length - 1;

    if(dropdown_length != 0) {

        // erase all options
        for (let i = dropdown_length; i >= 0; i--) {
            console.log(dropdown);
            dropdown.remove(i);
            console.log(dropdown);
            
        }
    }   
    
    // add in new data
    for (let i = 0; i < data.length; i++) {
        console.log(data[i]);
        let option = document.createElement('option');
        option.value = data[i]['id'];
        option.text = data[i]['name'] + ': £' + data[i]['price'];
        dropdown.add(option);

    }


}