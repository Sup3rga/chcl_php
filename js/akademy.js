$(function() {
   // $('.akademy').removeClass('template');
   // return;
   var userData = localStorage.getItem("akademy-data");
   localforage.getItem("akademy-data")
    .then(function(userData){
       if (userData == null) {
          DialogBox.setMessage("<icon class='ion-alert rounded red'></icon> Session invalide ! Veuillez vous connecter à nouveau.")
              .show().onClose(function () {
             window.location = "../";
          });
          return;
       }
       Modules.data = JSON.parse(userData);

       Fetch.get('./signin', {
          akauid: Modules.data.id,
          akatoken: Modules.data.token
       }).then(function (r) {
          if (r.code == 1) {
             Modules
                 .setSideMenu(Modules.data.privileges)
                 .setAppBar(Modules.data);
             $('.akademy').removeClass('template');
             Loader.dom = $('.waiter-load');
             History.init().reach();
          } else {
             localforage.removeItem("akademy-data");
             DialogBox.setMessage("<icon class='ion-alert rounded yellow'></icon> " + r.message)
                 .show().onClose(function () {
                window.location = "./";
             });
          }
       }).catch(function (e) {
          localStorage.removeItem("akademy-data");
          DialogBox.setMessage("<icon class='ion-alert rounded red'></icon> Une erreur est survénue ! Veuillez contacter le service webmastering.")
              .show().onClose(function(){
             window.location = "./";
          });
       });
    })
});