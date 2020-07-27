function get($url) {

  return new Promise(function (resolve, reject) {

    let req = new XMLHttpRequest();

    req.open('GET', $url, true);
    req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    req.onload = function() {

      if(req.status == 200) {
        resolve(req.response);
      }
      else {
        reject(Error(req.statusText));
      } 

    };

    req.onerror = function() {
      reject(Error("Network Error"));
    }

    req.send();

  });

}

function confirmationRecipt() {

  // /basket/checkout/confirmation

}

function spinner() {

  // const spinner = "<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
  //   < circle cx = "50" cy = "50" r = "50" />
  //   </svg >";

}

// LOGS IN USER (AJAX) THROUGH THE NAVIGATION BAR
function logInUser(form) {
  with(form) {

    console.log("work work work");
    var email = document.getElementById('login_email').value;
    var pass = document.getElementById('login_pass').value;

    const email_input = document.getElementById('login_email');
    const pass_input = document.getElementById('login_pass');



    if(email === '' || pass === '') {

      alert('nothing was entered');

    }
    else if (!email_input.checkValidity() || !pass_input.checkValidity()) {

      alert('user input invalid');

    } 
    
    else {


    let xhr = new XMLHttpRequest();
    xhr.open('POST', '/entry2.php', true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    //console.log(email + " " + pass);
    xhr.onload =  () => {
      // console.log("1");

      if (xhr.status === 200 && xhr.readyState === 4) {

        console.log(xhr.responseText + " hello response");
        //check whether what was passed starts with 'null'
        let check = xhr.responseText.slice(0,4);
        let account_menu = document.getElementById('account_menu');

        if(check === 'null') {
          // console.log("2");
          document.getElementById('login_error').innerHTML = 'no email / password match';
          account_menu.classList.toggle('login_error');

        } else {

          // console.log("3");
          setTimeout(() => {

            if (account_menu.classList.contains('login_error') === true) {
              account_menu.classList.toggle('login_error');

            } else {

              //method to add html to #nav_entry // for now
              document.getElementById("account_btn").innerHTML = '<a href="#"><i class="fi-list"></i><span>'+ xhr.responseText +'</span></a>';
              document.getElementById('active_login').classList.add('logged_in');
              account_menu.classList.toggle("entry_show");

              setTimeout(() => {

                document.getElementById('nav_entry').innerHTML = '<input class="button expanded" type="submit" value="Log out" name="log_out_user">';  
              }, 250);
              

            }
          }, 750);
          
        }
        // location.reload();
      } else {
        alert('Request failed.  Returned status of ' + xhr.status);
      }
    };

    xhr.send("email=" + email + "&password=" + pass);
    return false;
  }
  }

}
// refresh basket essential page
function getBasketData() {

  get('/basket').then(function(response) {
    document.body.innerHTML = response;
  }, function(error) {
    console.error("Failed", error);
  });

}
// remove basket data
function removeFromBasket($id, $slug, $index) {

  get(`/basket/remove/${$id}`).then(function(response) {

    getBasketData();

  }, function(error) {

    console.error("Failed" , error);
    
  });

}
// add to basket -> toggles basket
function addToBasket() {

  let $id = document.getElementById('basket_size').options[document.getElementById('basket_size').selectedIndex].value;

  get(`/basket/add/${$id}`).then(function (response) {

    // CHECK IF BASKET IS ACTING EMPTY
        if (document.getElementById('basket_nav_empty')) {
          // REMOVE EMPTY BASKET ELEMENTS
          document.getElementById('basket').innerHTML = '' + response + '<div class="button nav_basket_btn" id="basket_to_payment_btn"><a href="/basket">go to basket</a></div>';

        } else {
          document.getElementById('basket').innerHTML += response;
        }

        $count = parseInt(document.getElementById('basket_counter').innerText, 10) + 1;
        document.getElementById('basket_counter').innerHTML = $count;
        setTimeout(() =>{
          toggleBasket();
        }, 500)
        // open basket to show added item
        

        // debug
        // console.log(xhr.responseText);
  }, function (error) {

    console.error('Failed!', error);

  });


}
// search dropdown shows suggestions from user input
function showSuggest($str) {

    //create var for suggetion to go into
  var suggestions = document.getElementById('suggest_wrapper');
  //if string empty
  if ($str.length === 0) {
    suggestions.innerHTML = "";
    document.getElementById('nav_search').style.borderBottom = '1px solid #f00';
  } else {
    //set up AJAX Handler
    let xhr = new XMLHttpRequest();
    document.getElementById('nav_search').style.borderBottom = '1px solid #1dd1a1';

    xhr.open('GET', `/products/search/${$str}`, true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.onload = function () {

      if (xhr.readyState == 4) {

        if (xhr.status == 200) {

          suggestions.innerHTML = this.response;
          console.log(this.response);

        }
      }
    }
    xhr.send();
  }

}

function toggleBasket() {

  if(document.getElementById("account_menu").classList.contains("entry_show")) {
    toggleEntryPane();
  }

  document.getElementById("basket").classList.toggle("basket_show");
}

function toggleEntryPane() {
  if(document.getElementById('basket').classList.contains('basket_show')) {
    toggleBasket();
  }
  document.getElementById("account_menu").classList.toggle("entry_show");
}

function changeProductPageColour($slug) {

  // USE AJAX TO GET PRODUCT PAGE WITH SELECTED COLOUR
  location.replace(`/products/${$slug}`);

}

function selectedFilters() {

  let checkboxes = document.getElementsByClassName('filter_input');
  let newcheckboxesChecked = '';
  let checkboxesChecked = [];
  let sort = document.getElementsByName('sort');
  let sort_value = '';
  let checked = [];

  let endurl = '';

  let queryString = window.location.pathname.split('/');

  // check to see if a radio btn clicked
  for(i = 0; i < sort.length; i++) {
    if(sort[i].checked) {
      // console.log(sort[i].value);/
      sort_value = sort[i].value;
    }
  }
  
  // iterate through to see if any filters selcted
  for (var i = 0; i < checkboxes.length; i++) {

    // IF checked box checked
    if (checkboxes[i].checked) {
        
      let type = checkboxes[i].parentNode.parentNode.parentNode.id.split('_')[0];

      if(!checked.includes(type)) {

        checked.push(type , '/');

      }

      checked.splice(checked.indexOf(type) + 1, 0, ...['|',checkboxes[i].value]);
    }
  }

  param = checked.join('');


  // check to see if 'category' && 'filter' there, if so, filter down products from there
  $indexEl = queryString.indexOf('category');
  $indexFil = queryString.indexOf('filter');

  if($indexEl !== -1) {

    queryString.splice(0,$indexEl+1);

  }
  

  // is filter 
  if( queryString.indexOf('filter') !== -1 ) {

    let newurl = window.location.pathname.split('/');
    newurl.splice(newurl.indexOf('filter') + 1);

    console.log(param);

    console.log( newurl.join('/').concat('/' + param) );

    endurl = newurl.join('/').concat('/' + param);

    // window.location.href = (newurl.join('/')).concat('/' + param);

  }
  // // no filter
  else {

    console.log( window.location.pathname + '/filter/' + param )

    endurl = window.location.pathname + '/filter/' + param;

  }

  endurl = endurl.slice(0, -1);

  if( sort_value !== '' ) {

    window.location.href = endurl + '/sort/'  + sort_value;

  } else {

    window.location.href = endurl;
  }


}

function addPersonalInfo(arr) {
  let form = '';
  let pane = document.getElementById('account_form_pane');
  for (const [key, value] of Object.entries(arr)) {
    form += '<div class="edit_input_wrapper"><label>' + key + '</label><input type="text" id="edit_' + key + '" name="edit_' + key + '" value="' + value + '"></div>';
  }
  document.getElementById('edit_form_wrapper').innerHTML = form;
  pane.classList.toggle('account_edit_show');

}

function togglePersonalEdit() {
  document.getElementById('personal_info_edit').classList.toggle('account_edit_show');
}
// PARAM - TYPE OF INFO, ARRAY WITH SET INFO
function toggleEditInfo(type, arr) {

  let pane = document.getElementById('account_form_pane');

  if(type === 'personal') {

    // console.log('personal');
    //for each el
      // create label and input using key => value
    let form = '';
      for(const [key,value] of Object.entries(arr)) {
        form += '<div class="edit_input_wrapper"><label>'+key+'</label><input type="text" id="edit_'+key+'" name="edit_'+key+'" value="'+value+'"></div>';
      }
    document.getElementById('edit_form_wrapper').innerHTML = form;

  } 
  else if(type === 'address') {
    let form = '';
    for (const [key, value] of Object.entries(arr)) {
      form += '<div class="edit_input_wrapper"><label>' + key + '</label><input type="text" id="edit_' + key + '" name="edit_' + key + '" value="' + value + '"></div>';
    }
    document.getElementById('edit_form_wrapper').innerHTML = form;

  } 
  else if(type === 'payment') {
    console.log('personal');
  } 
  else {
    console.log('nothing');
  }
  // console.log(arr);
  pane.classList.toggle('account_edit_show');

}

function editAddress(id, arr) {
  console.log(id);
  console.log(arr);
  let pane = document.getElementById('account_form_pane');

  let form = '';

  for(const [key, value] of Object.entries(arr)) {

    form += '<div class="edit_input_wrapper"><label>' + key + '</label><input type="text" id="edit_' + key + '" name="edit_' + key + '" value="' + value + '"></div>';

  }
  document.getElementById('edit_form_wrapper').innerHTML = form;
  pane.classList.toggle('account_edit_show');
}

function clearProductFilters() {

  var checkboxes = document.getElementsByClassName('filter_input');
  var checkboxesChecked = '';

  for (var i = 0; i < checkboxes.length; i++) {
    // IF CHECKED PUT IT IN STRING
    if (checkboxes[i].checked) {
      checkboxes[i].checked = false;
    }

  }
  
  document.getElementById("filter_pane").classList.toggle("show_toggle_pane");

  let xmlhttp = new XMLHttpRequest();

  setTimeout(() => {
    xmlhttp.open("GET", "filter.php", true);

    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xmlhttp.onreadystatechange = function () {

      //check if everything good
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById('products_row').innerHTML = this.response;
      }
    }
    xmlhttp.send();

    
  },1000);

}

function sortProducts(sort) {

  // 
  // window.location.search.
  if (location.search.match(/sort=([^&]*)/i)) {
    // location.search.match(/zoom=([^&]*)/i) = sort;
    window.location.search.replace('sort', sort);
  }
  else {
    window.location.search += "&sort=" + sort;
  }

}

function toggleSuggestWrapper() {


  document.getElementById('suggest_bar_wrapper').classList.toggle('show_suggest');
  

}

function toggleSelectedFilter(filter) {

  let els = document.getElementsByClassName('filter_wrapper');


  for (let i = 0; i < els.length; i++) {
    if(els[i].classList.contains('show_filter_section')) {
      els[i].classList.toggle('show_filter_section');
    }
  };

  let parent = filter.parentNode;

  parent.classList.toggle('show_filter_section');

}

function toggleSortPane() {

  if(document.getElementById("filter_pane").classList.contains('show_toggle_pane')) {
    // TOGGLE OTHER PANE AWAY
    toggleFilterPane();
  }
  document.getElementById("sort_pane").classList.toggle("show_toggle_pane");
  document.getElementById('sort_products_btn').classList.toggle(('btn_clicked'));
}

function toggleFilterPane() {

  if (document.getElementById("sort_pane").classList.contains('show_toggle_pane')) {
    toggleSortPane();
  }
  document.getElementById("filter_pane").classList.toggle("show_toggle_pane");
  document.getElementById('filter_products_btn').classList.toggle(('btn_clicked'));
}

function inputError(input, message) {

  if(input.classList.contains('input_success')) {
    input.classList.toggle('input_success');
  }
  
  input.classList.toggle('invalid_input');

  // check if error already there
  let error_wrapper = '<div class="input_error_wrapper"></div>';
  let error_message = '<small class="invalid_input_alert">' + message +'</small>';

  let input_wrapper = input.parentElement.parentElement;

  if (input_wrapper.getElementsByClassName('input_error_wrapper').length === 0) {

    input_wrapper.innerHTML += error_wrapper;
    input_wrapper.getElementsByClassName('input_error_wrapper')[0].innerHTML = error_message;

  } else {

    let wrapper = input_wrapper.getElementsByClassName('input_error_wrapper')[0];
    let errors = wrapper.getElementsByClassName('invalid_input_alert');
    let gotErrorCheck = false;

    for(let error of errors) {
      if(error.innerText.trim() === message) {
        gotErrorCheck = true;
      }
    }
    if(!gotErrorCheck) {
      wrapper.innerHTML += error_message;
    }

  }

  // let error_small = input_wrapper.innerHTML += error_message;



}

function inputSuccess(input) {

  let input_wrapper = input.parentElement.parentElement;
  let errorWrapper = input_wrapper.getElementsByClassName('input_error_wrapper');

  if (input.classList.contains('invalid_input')) {
    input.classList.toggle('invalid_input');
    input_wrapper.getElementsByClassName('input_error_wrapper')[0].remove();
  }

  input.classList.toggle('input_success');

}

function checkSignupInput(input) {

  let form = document.forms['signup_form'];

  // console.log(input.target);
  // console.log(input.type);

  if(input.type === 'text') {

    if (!checkName(input)) {

      inputError(input, 'Name must be more than one letter');

    } else {
      inputSuccess(input);
    }

  } 
  else if(input.type === 'email') {

    if (!checkEmail(input)) {

      inputError(input, 'Enter a correct email format');

    } else {
      inputSuccess(input);
    }    

  } else if(input.type === 'password') {

    let password = form['password'];
    let verify_password = form['verify_password'];

    if (!checkStrongPassword(input) || password.value.trim() !== verify_password.value.trim()) {

      // document.getElementById('password_warning').classList.toggle('show_input_warning');

      inputError(password, 'Must contain at least one lower case letter <br>Must contain at least one uppercase letter<br>Must contain at least one number<br>Must contain at least one special character "!@#$%^&*-_"<br>Must be longer than 7 characters');
    } else {
      inputSuccess(password);
      inputSuccess(verify_password);
    }    

  }

}

function checkName(name) {
  return /^[a-z]{2,}$/ig.test(name.value.trim());
}

// returns bool if email format correct
function checkEmail(email) {

  return /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(email.value.trim());

}

// returns bool if strong password
function checkStrongPassword(password) {
  return /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*-_])(?=.{8,})/.test(password.value.trim());
}

function disableBtn(btn) {
  btn.classList.add('disabled_btn');
  btn.disabled = true;
  btn.onclick = null;
}

window.onload = function () {

let account_nav_btn = document.getElementById("account_btn");
let addToBasket = document.getElementById("add_to_basket");
let product = document.getElementsByClassName('product');
let basket_nav_btn = document.getElementById("basket_btn");
let remove_item_btn = document.getElementById("remove_item");
let imgs = document.getElementsByTagName("img");
let active_pp_img = document.getElementById('active_img');
let nav_menu_btn = document.getElementById('nav_menu');
let open_search = document.getElementById('open_search');
let nav_shop_dropdown = document.getElementById('nav_shop_link_list');
let basket_select_quantity = document.getElementById('basket_item_select');
let product_colour_select = document.getElementById('basket_colorway');
let form_signup = document.forms['signup_form'];
let hover_shop = document.getElementById('nav_shop_btn');


// hover_shop.addEventListener('mouseover', () => {

//   nav_shop_dropdown.style.display = 'flex';
//   nav_shop_dropdown.style.zIndex = '100';

//   hover_shop.addEventListener('mouseleave', () => {
//     nav_shop_dropdown.style.display = 'none';
//     nav_shop_dropdown.style.zIndex = '-1';
//   })

// });

  // if ( form_signup ) {

  // //   form_signup['submit'].disabled = true;

  // //   // ['type'] => [ regex, errortext ] 
  //   let fname = form_signup['firstname'];
  //   let lname = form_signup['lastname'];
  //   let email = form_signup['email'];
  //   let password = form_signup['password'];
  //   let verify_password = form_signup['verify_password'];

  //   form_signup.addEventListener('change', (e) => {

  //     checkSignupInput(e.target);

  //   });

  // }

  let flash = document.getElementById('flash_message');

  if(flash) {

    setTimeout(function () {

      flash.classList.add('hide_flash_header');

    }, 2000);

  }


  const email_input = document.getElementById('login_email');
  const pass_input = document.getElementById('login_pass');

  if(email_input) {

    let btn = document.getElementById('login_btn')

    email_input.addEventListener('change', () => {

      if (email_input.validity.valid && !pass_input.validity.valid) {
        if (btn.classList.contains('hidden_login_btn') === true) {

          btn.classList.remove('hidden_login_btn');
        }

        btn.onclick = () => { logInUser(this) };
      }
    })
}
// EVENT LISTENER FOR CHANGING OF PRODUCT QUANTITY IN BASKET
// check basket_product_quantity to see change



// ON PRODUCT PAGE, CHANGE COLOUR, SETS PAGE TO PRODUCT COLOR
  if (product_colour_select) {
    product_colour_select.addEventListener('change', (e) => {

      var selected = e.target.value;
      // console.log(selected);

      changeProductPageColour(selected);

    });
  }


if(nav_menu_btn) {
  nav_menu_btn.onclick = function () {
    // console.log('toggle nav');
    document.getElementById('nav-left').classList.toggle('nav_responsive_show');

  };
}

if(open_search) {
  open_search.onclick = function () {
    // console.log("work");
    document.getElementById('nav_search').classList.toggle('open_searchbar');
    document.getElementById('search_product').classList.toggle('search_bar_opened');
  }
}

if(account_nav_btn) {

  account_nav_btn.onclick = function () {

    toggleEntryPane();
  };
}


if(basket_btn) {
  basket_btn.onclick = function () {
    toggleBasket();
  };
}

if(product) {
  product.onmouseover = function () {
    document.getElementsByClassName('prod_info').classList.add('prod_hover');
  };
}


// basket_select_quantity.addEventListener('change', function () {
//   var selected = basket_select_quantity.selectedIndex;
//   if (selected != 2) {
//     console.log(selected);
//   }
// });

// remove_item_btn.onclick = function(e) {
//   console.log(e.target.id + ": 2222");
// };

// active_pp_img.addEventListener("load", function() {
//   console.log("all imgs have loaded");
// });
}
