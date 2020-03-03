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


    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/entry2.php', true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    //console.log(email + " " + pass);
    xhr.onload =  () => {
      console.log("1");

      if (xhr.status === 200 && xhr.readyState === 4) {

        console.log(xhr.responseText + " hello response");
        //check whether what was passed starts with 'null'
        let check = xhr.responseText.slice(0,4);
        let account_menu = document.getElementById('account_menu');

        if(check === 'null') {
          console.log("2");
          document.getElementById('login_error').innerHTML = 'no email / password match';
          account_menu.classList.toggle('login_error');

        } else {

          console.log("3");
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

// ADDS ITEM TO BASKET (AJAX) USING DATA FROM SELECTED PRODUCT PAGE

function addToBasket(form) {
  
  // INITIALIZE HTTP REQUEST
  var xhr = new XMLHttpRequest();

  var id = document.getElementById('add_to_basket_btn').getAttribute('value')
  var color = document.getElementById('basket_colorway').options[document.getElementById('basket_colorway').selectedIndex].value + "," + document.getElementById('basket_colorway').options[document.getElementById('basket_colorway').selectedIndex].innerHTML;
  var size = document.getElementById('basket_size').options[document.getElementById('basket_size').selectedIndex].value;


  xhr.open('GET', '/AJAX/add_to_basket.php?id=' + id + "&color=" + color + '&size=' + size, true);

  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

  
  
  xhr.onload = function() {
    // //check request done
    if(xhr.readyState == 4) {
      // checks status successful return
      if(xhr.status == 200) {

        document.getElementById('basket').innerHTML += xhr.responseText;
        console.log(xhr.responseText);

      } else {
        alert('Request failed.  Returned status of ' + xhr.status);
      }

    } else {
      console.log('not works');
    }

  };
  xhr.send("id=" + id + "&color=" + color + '&size=' + size);
  //use toggle to show basket and change button to 'added' to then transition back
  
  toggleBasket();
  // setTimeout(toggleBasket(),2000);
  // return false;

}

function showSuggest(str) {
  //create var for suggetion to go into
  var suggestions = document.getElementById('suggest_wrapper');
  //if string empty
  if(str.length === 0) {
    suggestions.innerHTML = "";
    document.getElementById('nav_search').style.borderBottom = '1px solid #f00';
  } else {
    //set up AJAX Handler
    var xmlhttp = new XMLHttpRequest();
    document.getElementById('nav_search').style.borderBottom = '1px solid #1dd1a1';

    xmlhttp.onreadystatechange = function() {
      if(this.readyState == 4 && this.status == 200) {

        // var suggestions = document.getElementById('suggest_wrapper');
        suggestions.innerHTML = this.response;
      }
    }
    xmlhttp.open("GET", "search.php?q=" + str, true);
    xmlhttp.send();
  }
}
function toggleBasket() {
  document.getElementById("basket").classList.toggle("basket_show");
};

function changeProductPageColour(prod_id, prod_name, colour_id) {

  // USE AJAX TO GET PRODUCT PAGE WITH SELECTED COLOUR
  location.replace('/product.php?prodid=' + prod_id + '&prodname=' +  prod_name + '&colour=' + colour_id);

}

function selectedFilters() {

  var checkboxes = document.getElementsByClassName('filter_input');
  var checkboxesChecked = '';
  var sort = document.getElementsByName('sort');
  var sort_value;

  for(i = 0; i < sort.length; i++) {
    if(sort[i].checked) {
      // console.log(sort[i].value);
      sort_value = sort[i].value;
    }
  }

  for (var i = 0; i < checkboxes.length; i++) {
    // IF CHECKED PUT IT IN STRING
    if (checkboxes[i].checked) {
      checkboxesChecked += checkboxes[i].value + ',';
    }

  }
  return window.location.href = '/products.php?filter=' + checkboxesChecked.substring(0, checkboxesChecked.length - 1) + '&sort=' + sort_value;

  // return checkboxesChecked;
}

// function getSelectedFilters() {

//   var checkboxes = document.getElementsByClassName('filter_input');
//   var checkboxesChecked = '';

//   console.log(checkboxes.length);

//   // loop over them all
//   for (var i = 0; i < checkboxes.length; i++) {
//     // IF CHECKED PUT IT IN STRING
//     if (checkboxes[i].checked) {
//       checkboxesChecked += checkboxes[i].value + ',';
//     }

//   }

//   var filter = checkboxesChecked.substring(0, checkboxesChecked.length - 1);
//   // Return the array if it is non-empty, or null
//   console.log(filter);

//   var xmlhttp = new XMLHttpRequest();

//   document.getElementById("filter_pane").classList.toggle("show_toggle_pane");

//   setTimeout(() => {
//     xmlhttp.open("GET", "filter.php?filter=" + filter, true);
//     // xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

//     xmlhttp.onreadystatechange = function () {

//       //check if everything good
//       if (this.readyState == 4 && this.status == 200) {
//         // products_row
//         document.getElementById('products_row').innerHTML = this.responseText;
//         // alert(filter);
//         url
//         console.log(this.response);
//       }
//     };
//     xmlhttp.send("filter= " + filter);
    
    
//   }, 1000);
  
  

// }

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

  var xmlhttp = new XMLHttpRequest();

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

window.onload = function () {

var account_nav_btn = document.getElementById("account_btn");
var addToBasket = document.getElementById("add_to_basket");
var product = document.getElementsByClassName('product');
var basket_nav_btn = document.getElementById("basket_btn");
var remove_item_btn = document.getElementById("remove_item");
var imgs = document.getElementsByTagName("img");
var active_pp_img = document.getElementById('active_img');
var nav_menu_btn = document.getElementById('nav_menu');
var open_serach = document.getElementById('open_search');
var nav_shop_dropdown = document.getElementById('nav_shop_link');
var basket_select_quantity = document.getElementById('basket_item_select');
var product_colour_select = document.getElementById('basket_colorway');
var k_f_title = document.getElementsByClassName('key_feature_title_wrapper');


// nav_shop_dropdown.addEventListener('mouseover', function() {
//   // console.log("???");
//   var dropdown_collections = document.getElementById('nav_shop_link_list');
//   dropdown_collections.classList.add('dropdown_shop_nav');
//   // dropdown_collections.addEventListener('mouseout', function() {
//   //   dropdown_collections.classList.remove('dropdown_shop_nav');
//   // });
//   // document.getElementById('nav').addEventListener('mouseout', function() {
//   //   dropdown_collections.classList.remove('dropdown_shop_nav');
//   // });
//   // nav_shop_dropdown.addEventListener('')
//
// });



// document.getElementsByClassName('color_of_product').addEventListener('hover', (e) => {

//   var hoverColor = e.style.backgroundColor;
//   console.log(hoverColor);

// });


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

// if(k_f_title) {
//   k_f_title.addEventListener('click', (e) => {
//     console.log(e.innerHTML);
//     e.parentNode.childNodes[1].style.display = 'flex';
//   });
// }

  // else if (!email_input.checkValidity() || !pass_input.checkValidity()) {
  //   alert('user input invalid');
  // } 


  if (product_colour_select) {
    product_colour_select.addEventListener('change', (e) => {

      var selectedColour = e.target.value;
      console.log(selectedColour);

      var url_string = window.location.href;
      var url = new URL(url_string);
      var prod_id = url.searchParams.get("prodid");
      var prod_name = url.searchParams.get("prodname");

      changeProductPageColour(prod_id, prod_name, selectedColour);



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
    console.log("work");
    document.getElementById('nav_search').classList.toggle('open_searchbar');
    document.getElementById('search_product').classList.toggle('search_bar_opened');
  }
}

if(account_nav_btn) {
  account_nav_btn.onclick = function () {

    document.getElementById("account_menu").classList.toggle("entry_show");
    //console.log("toggle");
  };
}


if(basket_btn) {
  basket_btn.onclick = function () {

    toggleBasket();
    //console.log("toggle");
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
