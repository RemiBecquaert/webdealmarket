

  var e = document.getElementById("country");
  function onChange() {
    var value = e.value;
    document.getElementById("countryCust").innerHTML = value;
  }
  e.onchange = onChange;
  onChange();
  
  function getFirstName(){
    var y = document.getElementById("firstname").value;
    document.getElementById("firstNameCust").innerHTML = y;
  
  }
  
  function getLastName(){
    var y = document.getElementById("lastname").value;
    document.getElementById("lastNameCust").innerHTML = y;
  
  }
  
  function getCompany(){
    var y = document.getElementById("company").value;
    document.getElementById("companyCust").innerHTML = y;
  
  }
  
  function getAddress(){
    var y = document.getElementById("address").value;
    document.getElementById("addressCust").innerHTML = y;
  
  }
  
  function getPostal(){
    var y = document.getElementById("postal").value;
    document.getElementById("postalCust").innerHTML = y;
  
  }
  
  function getCity(){
    var y = document.getElementById("city").value;
    document.getElementById("cityCust").innerHTML = y;
  
  }
  
  function getPhone(){
    var y = document.getElementById("phone").value;
    document.getElementById("phoneCust").innerHTML = y;
  
  }