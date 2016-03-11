var appControllers = angular.module('appControllers', ['ngAnimate', 'ngResource']);


appControllers.controller('mainController', ['$scope', '$http', '$location', function($scope, $http, $location) {
   
   $scope.$on("$routeChangeError", function(evt,current,previous,rejection){
    if(rejection == "not_logged_in"){
      console.log("mainCtrl: not logged in: redirecting");
      var serviceUrl = encodeURIComponent(config.baseUrl);
      console.log(serviceUrl);
      window.location.replace("https://login.gatech.edu/cas/login?service=" + serviceUrl);
    } else {
      //OR DO SOMETHING ELSE
    }
   });
   $scope.go = function(path) {

    $location.path(path);
    //$location.reload(true);
    //$scope.$parent.$apply();
  };

  $scope.ajaxError = function ajaxError(jqXHR, textStatus, errorThrown){
    console.log('ajaxError '+jqXHR+' '+textStatus+' '+errorThrown);
    console.log("Main Controller Called");
    // $location.path('/');
  }
}]);

appControllers.controller('HeaderController', ['$scope', '$http', '$location', function($scope, $http, $location) {
  
  console.log("Header Controller");
  $scope.$parent.headerType = {
    none: 1,
    mentee: 0,
    mentor: 0,
    admin: 0
  };
  $scope.headerType = $scope.$parent.headerType;

  $scope.refreshHeader = function() {
    
    var data = {};
    if(window.location.href.indexOf("welcome") > -1 || window.location.href.indexOf("register") > -1) {
      $scope.$parent.headerType.none = 1;
      $scope.$parent.headerType.mentee = 0;
      $scope.$parent.headerType.mentor = 0;
      $scope.$parent.headerType.admin = 0;
    } else {
      $.ajax({
          url: "api/user",
          dataType: "json",
          async: false,
          success: function(result) {
            data = result;
          },
          error: function(result) {
            $location.path("/loading");
          },
          type: 'GET'
        }); 
      if(data["Mentor"]) {
        $scope.$parent.headerType.none = 0;
        $scope.$parent.headerType.mentor = 1;
      }
      if(data["Mentee"]) {
        $scope.$parent.headerType.none = 0;
        $scope.$parent.headerType.mentee = 1;
      }
      if(data["Admin"]) {
        $scope.$parent.headerType.none = 0;
        $scope.$parent.headerType.admin = 1;
      }
    }
  }

  $scope.$parent.refreshHeader = $scope.refreshHeader;
  $scope.$on('$locationChangeSuccess', function() { 
    //add authentication validation?

    $scope.refreshHeader();
  });
}]);


appControllers.controller('ContactController', ['$scope','$location', function($scope,$location) {
}]);

appControllers.controller('EditProfileController', ['$scope', '$http', '$location','UserInfoService','FieldText', function($scope, $http, $location,$UserInfoService,$FieldText) {
  $scope.userData = $UserInfoService.getUser();
  $scope.username = $scope.userData['Username'];
  $scope.form = {};
  $scope.field_text = {};
  $scope.mentor_specific = false;
  $scope.userInfo = $UserInfoService.userInfo;
  $scope.validation = false;
  $('.ui.checkbox').checkbox();
  $('select.dropdown').dropdown();
  
  $('.ui.form')
    .form($UserInfoService.ui_rules,{
      inline: true,
      on: 'blur',
      transition: 'fade down', 
      onSuccess: function() {
        $scope.validation = true;
      },
      onFailure: function() {
        $scope.validation = false;
      }
    });

  $('form').submit(function(e){
    e.preventDefault();
    $('.ui.form').form('validate form'); 
    // $scope.submitEdit();
  });


  if($scope.userData["Mentor"]) {
    $scope.mentor_specific = true;
    // $scope.viewMentorForm = 1;
    // $scope.viewMenteeForm = 0;
    $scope.field_text = $FieldText.mentor_field_text;
    $.get('api/mentor/' + $scope.username).success(function(data) {
      $scope.data = JSON.parse(data)[0];
      $scope.form = $UserInfoService.data_expand($scope.data);
      $scope.userInfo = $UserInfoService.update_description($scope.form.breadth_track,$scope.userInfo);
      console.log($scope.form);
      $scope.form.load = true;
      $scope.$apply();
    });
  }
  if($scope.userData["Mentee"]) {
    // $scope.viewMenteeForm = 1;
    // $scope.viewMentorForm = 0;
    $scope.field_text = $FieldText.mentee_field_text;
    $.get('api/mentee/' + $scope.username).success(function(data) {
      $scope.data = JSON.parse(data)[0];
      $scope.form = $UserInfoService.data_expand($scope.data);
      $scope.userInfo = $UserInfoService.update_description($scope.form.breadth_track,$scope.userInfo);
      console.log($scope.data);
      $scope.form.load = true;
      $scope.$apply();
    });
  }

  $scope.toggleSelection = function toggleSelection (opt, attr) {

    var idx = $scope.form[attr].indexOf(opt);
    if(idx > -1) {
      opt.display = 0;
      $scope.form[attr].splice(idx, 1);
    }
    else {
      opt.display = 1;
      $scope.form[attr].push(opt);
    }
    console.log($scope.form[attr]);
  };

  $scope.newValue = function(value, attr) {
    console.log($scope.form[attr]);
    $scope.form[attr] = value;
    if (value == "Other" && attr == "dfocus") {
      $scope.form.dfocusother = $scope.form.dfocusother; //left side was $scope.form.dfocus.other
    } else if (value != "Other" && attr == "dfocus") {
      $scope.form.dfocusother = null;
    }
    if (value && attr == "transfer_from_within") {
      $scope.form.transfer_from_outside = null;
      // $scope.form.transfer_from_outside = 0;
      $scope.form.institution_name = null;
    } else if (value && attr == "transfer_from_outside") {
      $scope.form.transfer_from_within = 0;
      $scope.form.prev_major = null;
    } else if (!value && attr == "transfer_from_outside") {
      $scope.form.transfer_from_outside = 0;
      $scope.form.transfer_from_within = 0;
      $scope.form.institution_name = null;
      $scope.form.prev_major = null;
    }
    if (value == 'No' && attr == "majorHelper") { //if they do not want another major, set other_major = 0
      $scope.form.other_major = null;
      // $('.ui.form#other_major').form('clear');
      $('#other_major').trigger('reset');
    } 
    if (!value && attr == "undergrad_research") {
      $scope.form.undergrad_research_desc = null;
    }
  }

  $scope.submitEdit = function() {
    $scope.submitData = $UserInfoService.editprofiledata($scope.form);
    if($scope.validation) {
      if($scope.userData["Mentor"]) {
        $.ajax({
          url: "api/mentorUpdate",
          dataType: "json",
          async: false,
          data: $scope.submitData,
          type: 'POST',
          success: $scope.success()
          // error: ajaxError
        }); 
      }else {
        $.ajax({
          url: "api/menteeUpdate",
          dataType: "json",
          async: false,
          data: $scope.submitData,
          type: 'POST',
          success: $scope.success()
          // error: ajaxError
        });
      }
    }
  }
  
  $scope.success = function() {
    console.log("Update successful");
    $location.path('/homescreen');
  }

}]);

appControllers.controller('WelcomeController', ['$scope', '$http', '$location', function($scope, $http, $location) {
  $scope.go = function() {
    var serviceUrl = encodeURIComponent(config.baseUrl);
    console.log(serviceUrl);
    window.location.replace("https://login.gatech.edu/cas/login?service=" + serviceUrl);
  };
}]);

appControllers.controller('LogoutController', ['$scope', '$http', '$location', function($scope, $http, $location) {
  document.cookie = "_ga=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;";
  document.cookie = "PHPSESSID=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;";
  var serviceUrl = encodeURIComponent(config.baseUrl);
  window.location.replace("https://login.gatech.edu/cas/logout?service=" + serviceUrl);
}]);

appControllers.controller('ForkController', ['$scope', '$http', function($scope, $http) {

}]);

appControllers.controller('LoadingController', ['$location','$scope', '$http', function($location,$scope, $http) {
  console.log('loading first');
  // if($scope.$parent.headerType == null) {
  //   $location.url('/welcome');
  // } else {
  //   $location.url('/homescreen');  
  // }
}]);


// appControllers.controller('UserController', ['$scope', '$http','$location', function($scope, $http,$location) {
//   console.log("before calling welcome api *********")
//   $.get('api/welcome').success(function(data) {
//     $scope.user = data;
//     $scope.userType = data['userType'];
//     $scope.$parent.username = data['username'];
//   }).error(function(data) {
//     if(data.status == 403) {
//       console.log(data.status);
//       // $location.path('/welcome');
//     }
//   });
// }]);

appControllers.controller('UserController', ['$scope', '$http', '$location', function($scope, $http, $location) {
  
  $scope.user = {type:[],
    none: 1,
    mentee: 0,
    mentor: 0,
    admin: 0,
    id: ''};
  var data = {};
  
  $.ajax({
      url: "api/user",
      dataType: "json",
      async: false,
      success: function(result) {
        data = result;
      },
      type: 'GET',
      // error: $ajaxError
    });
  $scope.user.name = data["Name"];
  $scope.$parent.username = data["Name"];
  // $scope.user.id = data["Id"];
  $scope.user.mentor = data["Mentor"];
  $scope.user.mentee = data["Mentee"];
  $scope.user.admin = data["Admin"];
  if ($scope.user.mentor == 1 || $scope.user.mentee == 1 || $scope.user.admin ==1) {
    $scope.user.none = 0;
  }

  if(data["Admin"]) {
    $scope.user.type.push("Admin");
    $scope.widgets = [
    {
      // TODO: create image
      image: "/images/wireframe/image.png",
      title: "Toggle Requesting Period",
      description: "Open and close the requesting period for mentors",
      meta: "Meta",
      link: "#/requestingPeriod"
    },
    {
      // TODO: create image
      image: "/images/wireframe/image.png",
      title: "View Matches",
      description: "View mentor/mentee matches and unmatched users",
      meta: "Meta",
      link: "#/viewMatches"
    },
    {
      // TODO: create image
      image: "/images/wireframe/image.png",
      title: "Approve Mentors",
      description: "Approve registered users to mentor students",
      meta: "Meta",
      link: "#/approveMentors"
    },
    {
      // TODO: create image
      image: "/images/wireframe/image.png",
      title: "Mentor Max",
      description: "Set the maximum number of mentees any one mentor can have per semester",
      meta: "Meta",
      link: "#/setMentorMax"
    }];
  }

  else if (data["Mentor"]) {
    console.log("Its a mentor")
    $scope.user.type.push("Mentor");
    $scope.mentees = [];
    $.ajax({
      url: "api/getMentorMatches",
      dataType: "json",
      async: true,
      success: function(result) {
        $scope.mentees = result;
        $scope.$apply();
      },
      error: $scope.ajaxError
    });
  }
  else if (data["Mentee"]) {
    $scope.user.type.push("Mentee");
    $scope.profile_title = "Your Mentor";

    function getMentorData(mentorUsername) {
      $.ajax({
        url: "api/mentor/" + mentorUsername,
        dataType: "json",
        async: true,
        success: function(result) {
          $scope.show_identifier = true;
          $scope.myMentor = result[0];
          $scope.$apply();
        },
        type: 'GET',
        error: $scope.ajaxError
      });
    }

    $.ajax({
      url: "api/getMenteeMatch",
      dataType: "json",
          async: true,
          success: function(data, textStatus, jqXHR) {
            if(data != ""){
              getMentorData(data[0].mentor_user);
            }
          },
          error: $scope.ajaxError
    });
  }
  

}]);

appControllers.controller('HouseController', ['$scope','HouseService','TaskService','UserInfoService','$route', function($scope,$HouseService,$TaskService,$UserInfoService,$route) {
  $scope.user = $HouseService.getUser(); 
  $scope.username = $scope.user['Username'];
  $scope.houses = $HouseService.getHouses();
  $scope.houseMembers = $HouseService.getHouseMembers();
  console.log($scope.houses);
  console.log($scope.user);
  console.log($scope.houseMembers);
  $scope.dd_title  = 'Select task type';
  $scope.task_selected = true;
  $scope.validation = false;
  $scope.sub_suc_show = false;
  $scope.prevent_sub = false;
  $scope.submit_task = {};
  $('.ui.dropdown').dropdown();
  $('.house-family-tab.menu .item').tab({history:false});
  $('table').tablesort();
  $('.ui.form')
    .form({
      date: {
        identifier  : 'date_input',
        rules: [
          {
            type   : 'empty',
            prompt : 'Please enter task date'
          }
        ]
      },
      desc: {
        identifier  : 'desc_input',
        rules: [
          {
            type   : 'empty',
            prompt : 'Please enter task description'
          }
        ]
      }
    },
    {
      on: 'blur',
      onSuccess: function() {
        $scope.validation = true;
      },
      onFailure: function() {
        $scope.validation = false;
      }
    });


  $scope.tasks = $TaskService.tasks;

  $scope.toggleMessage = function() {
    $scope.sub_suc_show = !$scope.sub_suc_show;
  }

  $scope.getColor = function() {
    switch ($scope.user.house_belongs){
      case 'Crimson':
        return 'red';
        break;
      case 'Amber':
        return 'yellow';
        break;
      case 'Emerald':
        return 'green';
        break;
      case 'Sapphire':
        return 'blue';
        break;
    }
  }
  
  $scope.setTitle = function(title,task) {
    $scope.dd_title = title;
    $('.ui.dropdown').dropdown('toggle');
    $scope.task_selected = true;
    $.extend($scope.submit_task,task);
  }

  $scope.submit = function() {
    if(!$scope.submit_task.task_id) {
      $scope.task_selected = false;
    } else if ($scope.prevent_sub) {
      alert("Please don't submit the same task twice");
    } else if($scope.validation) {
      $scope.submit_task.task_date = $TaskService.yyyymmdd($scope.date);
      console.log($scope.submit_task);
      $.ajax({
          url: "api/task",
          dataType: "json",
          async: false,
          data: $scope.submit_task,
          type: 'POST',
          success: $scope.success()
          // error: ajaxError
        }); 
    }
  }

  $scope.success = function() {
      // $route.reload();
      // $('form').form('clear');
      $scope.sub_suc_show = true;
      $scope.prevent_sub = true;
      setTimeout(function(){$route.reload();}, 4000);
      console.log("Successful");
  }

}]);

appControllers.controller('SearchController', ['$scope', '$http', function($scope, $http) {
  var open = {};
  $.ajax({
    url: "api/requestPeriodStatus",
    dataType: "json",
    async: false,
    type: 'GET',
    success: function(data) {
      open = data;
    }
  });
  $.ajax({
    url: "api/getMenteeMatch",
    dataType: "json",
      async: false,
      success: function(data) {
        console.log(data.length == 0);
        console.log(open['isOpen'] == '1');
        $scope.chooseAvailable = (data.length == 0 && open['isOpen'] == '1');
      },
      error: $scope.ajaxError
  });

  $('.ui.checkbox').checkbox();
  $('.ui.accordion').accordion();

  $.ajax({
    url: "api/listApprovedMentors",
    dataType: "json",
      async: true,
      success: function(data, textStatus, jqXHR) {
        console.log(data);
        $scope.userData = data;
        $scope.miniProfileData = $scope.userData[0];
        $scope.wishButton = {};
        $scope.renderButton($scope.miniProfileData.favorited);
        $scope.refreshUI();
        $scope.$apply();

      },
      error: $scope.ajaxError
    });

  $scope.showFull = function(user) {
    $scope.profile_title = "Mentor Profile";
    $scope.show_identifier = false;
    $scope.myMentor = user;
    $scope.show_full_profile = true;
  }
  $scope.hideFull = function() {
    $scope.show_full_profile = false;
  }
  $scope.miniProfileSet = function(user) {
    $scope.miniProfileData = user;
    $scope.renderButton($scope.miniProfileData.favorited);
  }
  $scope.addToWishlist = function() {
    $scope.miniProfileData.favorited = "favorited";
    $scope.renderButton($scope.miniProfileData.favorited);
    $.ajax({
      url: "api/wishlist",
      async: true,
      data: {'username': $scope.miniProfileData.username},
      type: 'POST'
    });
  }
  $scope.removeFromWishlist = function() {
    $scope.miniProfileData.favorited = "";
    $scope.renderButton($scope.miniProfileData.favorited);
    $.ajax({
      url: "api/wishlist/" + $scope.miniProfileData.username,
      async: true,
      type: 'DELETE'
    }); 
  }
  $scope.refreshUI = function() {
    var wishlist = [];
    $.ajax({
      url: "api/wishlist",
      dataType: "json",
      async: false,
      success: function(result) {
        wishlist = result;
      },
    });

    $scope.userData.forEach(function(element) {
      var user = element;
      wishlist.forEach(function(element) {
        if (user.username === element.username) {
          user.favorited = "favorited";
        }
      });
    });
  }
  $scope.notification = function() {
    // check for mentor being full first
    $.ajax({
        url: "api/mentorStatus/" + $scope.miniProfileData.username, 
        async: true,
        type: 'GET',
        success: function(result) {
    if(result){
      $('#mentor-note').dimmer('toggle');
    }
    else{
      alert("It appears that this mentor is no longer available, please select another one.");
    }
        },
      });
    
    //$('#mentor-note').dimmer('toggle');
  }
  $scope.toggleNote = function() {
    $('#mentor-note').dimmer('toggle');
  }
  $scope.chooseMentor = function() {
    $scope.$parent.myMentor = $scope.miniProfileData;
    $scope.myMentor = $scope.$parent.myMentor;

    $.ajax({
      url: "api/chooseMentor",
      dataType: "json",
      data: {'mentor': $scope.myMentor.username}, //$scope.$parent.myMentor
      type: 'POST',
    async: false,
    success: function(data){
      $scope.go('/user-profile');
    },
    error: function(data) {
      console.log(data);
    }
    }); 
  }
  $scope.$on('$routeChangeStart', function () { //For some reason the isotope ul must be emptied or page change lags
    $('#isotopeContainer').empty();
  });
  $scope.renderButton = function(favorited) {
    if (favorited == "favorited") {
      $scope.wishButton.contentText = "Remove from Wishlist";
      $scope.wishButton.fn = $scope.removeFromWishlist;
    } else {
      $scope.wishButton.contentText = "Add to Wishlist";
      $scope.wishButton.fn = $scope.addToWishlist;
    }
  }
}]);

appControllers.controller('WishListController', ['$scope', '$http', function($scope, $http) {
  var open = {};
  $.ajax({
    url: "api/requestPeriodStatus",
    dataType: "json",
    async: false,
    type: 'GET',
    success: function(data) {
      open = data;
    }
  });
  $.ajax({
    url: "api/getMenteeMatch",
    dataType: "json",
      async: false,
      success: function(data) {
        console.log(data.length == 0);
        console.log(open['isOpen'] == '1');
        $scope.chooseAvailable = (data.length == 0 && open['isOpen'] == '1');
      },
      error: $scope.ajaxError
  });

  $scope.userData = [];
  $.ajax({
      url: "api/wishlist",
      dataType: "json",
      async: false,
      success: function(result) {
        $scope.userData = result;
      },
    });
  $scope.userData.forEach(function(element) {
    element.favorited = "favorited";
  });
  if ($scope.userData) {
    $scope.miniProfileData = $scope.userData[0];
  }

  $scope.showFull = function(user) {
    $scope.profile_title = "Mentor Profile";
    $scope.show_identifier = false;
    $scope.myMentor = user;
    $scope.show_full_profile = true;
  }
  $scope.hideFull = function() {
    $scope.show_full_profile = false;
  }
  $scope.miniProfileSet = function(user) {
    $scope.miniProfileData = user;
  }
  $scope.notification = function() {

    // check for mentor being full first
    $.ajax({
        url: "api/mentorStatus/" + $scope.miniProfileData.username, 
        async: true,
        type: 'GET',
        success: function(result) {
    if(result){
      $('#mentor-note').dimmer('toggle');
    }
    else{
      alert("It appears that this mentor is no longer available, please select another one.");
    }
        },
      });
    //$('#mentor-note').dimmer('toggle');
  }
  $scope.removeFromWishlist = function() {
    $scope.miniProfileData.favorited = "";
    $.ajax({
      url: "api/wishlist/" + $scope.miniProfileData.username,
      async: true,
      type: 'DELETE'
    }); 
    $.each($scope.userData, function(i){
      if($scope.userData[i].username === $scope.miniProfileData.username) {
        $scope.userData.splice(i,1);
        return false;
      }
    });
  }
  $scope.chooseMentor = function() {
    $scope.$parent.myMentor = $scope.miniProfileData;
    $scope.myMentor = $scope.$parent.myMentor;

    $.ajax({
      url: "api/chooseMentor",
      dataType: "json",
      data: {'mentor': $scope.myMentor.username}, //$scope.$parent.myMentor
      type: 'POST',
      async: false,
      success: function(data){
        $scope.go('/user-profile');
      },
      error: function(data) {
        console.log(data);
      }
    });
  }
  $scope.refreshUI = function() {
    $scope.userData.forEach(function(element) {
      var user = element;
      $scope.$parent.wishList.forEach(function(element) {
        if (user.username === element.username) { //JSON.stringify(user) === JSON.stringify(element)
          user.favorited = "favorited";
        }
      });
    });
  }
}]);

appControllers.controller('ApproveMentorController', ['$scope', '$http', function($scope, $http) {
  $scope.mentors = []
  $.ajax({
    url: "api/listUnapprovedMentors",
    dataType: "json",
    async: true,
    success: function(data) {
      console.log(data);
      $scope.mentors = data;
      $scope.$apply();
    }
  });

  $scope.approve = function() {
    var usernames = []
    $scope.mentors.forEach(function(element) {
      if (element.newapprove) {
        usernames.push(element.username);
      }
    });
    $.ajax({
      url: "api/approveMentor",
      dataType: "json",
      async: true,
      type: 'POST',
      data: {'usernames': usernames}
    });
    $scope.go('/homescreen');
  };
}]);

appControllers.controller('RequestingPeriodController', ['$scope', '$http', function($scope, $http) {
  var open = {};
  $.ajax({
    url: "api/requestPeriodStatus",
    dataType: "json",
    async: false,
    type: 'GET',
    success: function(data) {
      open = data;
    }
  });

  if (open['isOpen'] == '1') {
    $scope.action_case = "Close"
    $scope.action_lower = "close"
    $scope.explanation = "(This will prevent current mentees from selecting any mentors.)"
  } else {
    $scope.action_case = "Open"
    $scope.action_lower = "open"
    $scope.explanation = "(This will allow current mentees to select a mentor.)"
  }

  $scope.notification = function() {
    $('#mentor-note').dimmer('toggle');
  }
  $scope.triggerRequestingPeriod = function() {
    if (open['isOpen'] == '1') {
      $.ajax({
        url: "api/requestPeriodStatus",
        dataType: "json",
        data: {'isOpen': 0},
        async: false,
        type: 'POST'
      });
    } else {
      $.ajax({
        url: "api/requestPeriodStatus",
        dataType: "json",
        data: {'isOpen': 1},
        async: false,
        type: 'POST'
      });
    }
    $scope.go('/homescreen');
  }
}]);

appControllers.controller('ViewMatchesController', ['$scope', '$http', function($scope, $http) {
  $scope.notification = function() {
    $('#match-note').dimmer('toggle');
  };
  $scope.matchAll = function() {
    $.ajax({
      url: "api/assignAllMentees",
      async: true,
      type: 'POST'
    });
    $scope.go('/homescreen');
  };

  var matchesList = [];
  var unmatchedMentors = [];
  var unmatchedMentees = [];
  $.ajax({
    url: "api/getMatches",
    dataType: "json",
    async: false,
    success: function(result) {
      matchesList = result;
    }
  });
  $.ajax({
    url: "api/getUnmatchedMentors",
    dataType: "json",
    async: false,
    success: function(result) {
      unmatchedMentors = result;
    }
  }); 
  $.ajax({
    url: "api/getUnmatchedMentees",
    dataType: "json",
    async: false,
    success: function(result) {
      unmatchedMentees = result;
    }
  }); 

  var matches = {};
  matchesList.forEach(function (match) {
    var mentor = {
      first_name: match.mentor_first_name,
      last_name: match.mentor_last_name,
      username: match.mentor_username
    }
    var mentee = {
      first_name: match.mentee_first_name,
      last_name: match.mentee_last_name,
      username: match.mentee_username
    }
    
    if (mentor.username in matches) {
      matches[mentor.username].mentees.push(mentee);
    } else {
      matches[mentor.username] = mentor;
      matches[mentor.username].mentees = [mentee];
    }
  });

  var mentors = [];
  for (var mentor in matches) {
    mentors.push(matches[mentor]);
  }

  $scope.mentors = mentors;
  $scope.unmatchedMentors = unmatchedMentors;
  $scope.unmatchedMentees = unmatchedMentees;
}]);

appControllers.controller('SetMentorMaxController', ['$scope', '$http', function($scope, $http) {
  var max = {};
  var min = {};
  $.ajax({
    url: "api/mentorMax",
    dataType: "json",
    async: false,
    type: 'GET',
    success: function(data) {
      max = data;
      $scope.maxNumber = max;
    }
  });
  $.ajax({
    url: "api/minMentorMax",
    dataType: "json",
    async: false,
    type: 'GET',
    success: function(data) {
      min = data;
      $scope.minNumber = min;
    }
  });

  $scope.notification = function() {

    var newMaxVal = $('#max_number').val();
    if(newMaxVal == '' || newMaxVal < $scope.minNumber){
      alert("Note: you must enter a value for the new max");
    }
    else{
    $('#mentor-note').dimmer('toggle');
    }
  }
    $scope.triggerSetMax = function(data) {
    var newMaxVal = $('#max_number').val();
    
    // save new max val
    $.ajax({
        url: "api/mentorMax",
        dataType: "json",
        data: {'newMax': newMaxVal},
        async: false,
        type: 'POST',
        success: function(data){
          $scope.go('/homescreen');
        }
      });
  }
}]);

appControllers.controller('UserProfileController', ['$scope', '$http', '$location', function($scope, $http, $location) {
  $scope.myMentor = $scope.$parent.myMentor;

  $scope.reset = function() {
    $.ajax({
      url: "api/resetUser",
      dataType: "json",
      async: true,
      success: function(result) {
        //data = result;
      },
      type: 'GET'
      // error: ajaxError
    }); 
  }
}]);

appControllers.controller('RegisterController', ['$scope', '$http', '$location', function($scope, $http, $location) {
}]);

appControllers.controller('RegisterMenteeController', ['$scope', '$http', '$filter', '$location','UserInfoService', function($scope, $http, $filter, $location, $UserInfoService) {
  $('.ui.radio.checkbox').checkbox();
  $('.ui.checkbox').checkbox();
  $('.ui.dropdown').dropdown();

  $UserInfoService.formui();
  $scope.userInfo = $UserInfoService.userInfo;
  $scope.showNext = $scope.$parent.showNext;

  $scope.form = { 
      dfocus: '', 
      breadth_track:[],
      bme_organization: [],
      tutor_teacher_program: [],
      bme_academ_exp: [],
      international_experience: [],
      career_dev_program: [],
      other_major: null
  };

  $('form').submit(function(e){
    e.preventDefault();
    $('.ui.form').form('validate form');

  });


  $scope.toggleSelection = function toggleSelection (opt, attr) {
    var idx = $scope.form[attr].indexOf(opt)
    if(idx > -1) {
      $scope.form[attr].splice(idx, 1);
    }
    else {
      $scope.form[attr].push(opt);
    }
  };

  $scope.newValue = function(value, attr) {
    console.log('new value', value);
    $scope.form[attr] = value;
    if (value == "Other" && attr == "dfocus") {
      $scope.form.dfocusother = $scope.form.dfocusother; //left side was $scope.form.dfocus.other
    } else if (value != "Other" && attr == "dfocus") {
      $scope.form.dfocusother = null;
    }
    if (!value && attr == "majorHelper") { //if they do not want another major, set other_major = 0
      $scope.form.other_major = null;
    } 
    if (value && attr == "transfer_from_within") {
      $scope.form.transfer_from_outside = null;
      $scope.form.institution_name = null;
    } else if (value && attr == "transfer_from_outside") {
      $scope.form.transfer_from_within = 0;
      $scope.form.prev_major = null;
    } else if (!value && attr == "transfer_from_outside") {
      $scope.form.transfer_from_outside = 0;
      $scope.form.transfer_from_within = 0;
      $scope.form.institution_name = null;
      $scope.form.prev_major = null;
    }
  }

 
  $scope.addMentee = function() {
    // console.log($UserInfoService.wrapMenteeData($scope.form))
    $.ajax({
      url: "api/mentee",
      dataType: "json",
      async: false,
      data: $UserInfoService.wrapMenteeData($scope.form),
      type: 'POST',
      success: $scope.success()
      // error: ajaxError
    }); 
  };

  $scope.success = function() {
    $scope.$parent.showNext = true;
    $scope.showNext = $scope.$parent.showNext;
  }

}]);


appControllers.controller('RegisterMentorController', ['$scope', '$http', '$filter', '$location','UserInfoService', function($scope, $http, $filter, $location,$UserInfoService) {
  $scope.showNext = $scope.$parent.showNext;
  $UserInfoService.formui();

  $scope.userInfo = $UserInfoService.userInfo;


  // // $('.ui.form').form('validate form');

  $('form').submit(function(e){
    e.preventDefault();
    $('.ui.form').form('validate form');

  });

  $scope.form = { 
      dfocus: null, 
      ethnicity: [],
      honor_program: [],
      breadth_track:[],
      bme_organization: [],
      mm_org: [],
      tutor_teacher_program: [],
      bme_academ_exp: [],
      international_experience: [],
      career_dev_program: []
  };

  $scope.toggleSelection = function toggleSelection (opt, attr) {
    var idx = $scope.form[attr].indexOf(opt)
    if(idx > -1) {
      $scope.form[attr].splice(idx, 1);
    }
    else {
      $scope.form[attr].push(opt);
    }
  };

  $scope.newValue = function(value, attr) {
    $scope.form[attr] = value;
    if (value == "Other" && attr == "dfocus") {
      $scope.form.dfocusother = $scope.form.dfocusother; //left side was $scope.form.dfocus.other
    } else if (value != "Other" && attr == "dfocus") {
      $scope.form.dfocusother = null;
    }
    if (value && attr == "transfer_from_within") {
      $scope.form.transfer_from_outside = null;
      $scope.form.institution_name = null;
    } else if (value && attr == "transfer_from_outside") {
      $scope.form.transfer_from_within = 0;
      $scope.form.prev_major = null;
    } else if (!value && attr == "transfer_from_outside") {
      $scope.form.transfer_from_outside = 0;
      $scope.form.transfer_from_within = 0;
      $scope.form.institution_name = null;
      $scope.form.prev_major = null;
    }
    if (!value && attr == "majorHelper") { //if they do not want another major, set other_major = 0
      $scope.form.other_major = null;
    } 
    if (!value && attr == "undergrad_research") {
      $scope.form.undergrad_research_desc = null;
    } 
  }

  $scope.addMentor = function addMentor(validation) {
    console.log("addMentor Function");

    if(validation) {
      console.log("validation is true");
      $.ajax({
        url: "api/mentor",
        dataType: "json",
        async: false,
        data: $UserInfoService.wrapMentorData($scope.form),
        type: 'POST',
        success: $scope.success()
        // error: ajaxError
      });
    }
    console.log("outside if statement" );
  }

  $scope.success = function() {
    $scope.$parent.showNext = true;
    $scope.showNext = $scope.$parent.showNext;
  }
  
}]);

appControllers.controller('MentorUserAgreementController', ['$scope', '$http', '$location', function($scope, $http, $location) {
  $('.ui.radio.checkbox').checkbox();
  var ind = 0;
  $scope.active = ind;
  $scope.form= {
    "q1": 0,
    "q2": 0,
    "q3": 0,
    "q4": 0,
    "q5": 0,
    "q6": 0,
    "q7": 0,
    "q8": 0,
    "q9": 0,
    "q10": 0,
    "q11": 0,
    "q12": 0,
    "q13": 0,
    "q14": 0,
    "q15": 0
  };
  $scope.yes = false;


  $scope.yesno = [{
      id:1,
      name: 'Yes',
      value: 1
    }, {
      id:2, 
      name: 'No',
      value: 0
    }];

    $scope.yesnoOptIn = [{
      id:1,
      name: 'Yes, I commit to serving as a mentor next fall.',
      value: 1
    }, {
      id:2, 
      name: 'No, I do not wish to serve as a mentor next fall.',
      value: 0
    }];

  $scope.newValue = function(value, attr) {
    $scope.form[attr] = value;
  };

  $scope.setStep = function(index) {
    // if (!$(this).hasClass('disabled')) {
    $('.ui.steps div').removeClass('active');
    $('.ui.steps div:eq(' + index + ')').addClass('active');
    $scope.active = index;
  };

  $scope.allYes = function() {
    var numTrue = 0;
    $.each($scope.form, function(key, value) {
      console.log($scope.form);
      if (value === 1 && value !== 0) {
        numTrue++;
        console.log("true" + value);
      } else {
        console.log("false" + value);
      }
    });
    if (numTrue == 15) {
      $scope.yes = true;
      $location.path('/mentorReg');
      window.scrollTo(0,0);
    }
  };
}]);

appControllers.controller('MentorAliasController', ['$scope', '$http', '$location', function($scope, $http, $location) {
   $scope.aliasNames;
   var color;
   var adjective;
   var animal; 
   var alias;
  $scope.generate = function() {
    $scope.generateClicked = true;
    var validName = false;
    function nameRequest() {
      $http.get('aliasNames.json').success(function(data){
           $scope.aliasNames = data;
          // console.log(Math.floor(Math.random()*$scope.aliasNames[0].color.length));
          var randoNum = Math.random();
          $scope.color = $scope.aliasNames[0].color[Math.floor(randoNum * $scope.aliasNames[0].color.length)].name;
          $scope.hex = $scope.aliasNames[0].color[Math.floor(randoNum * $scope.aliasNames[0].color.length)].hex;
          $scope.adjective = $scope.aliasNames[1].adjective[Math.floor(Math.random() * $scope.aliasNames[1].adjective.length)];
          $scope.animal = $scope.aliasNames[2].animal[Math.floor(Math.random() * $scope.aliasNames[2].animal.length)];
          alias = $scope.color + " " + $scope.adjective + " " + $scope.animal;
          $http.get('api/alias/'+ alias).success( function(data) {
            // adjust error message from GET RED ERROR to be specially defined
            console.log("there is an existing name, can't be used");
            return validName = false;
          })
          .error( function(data) {
            console.log("there's no existing name, this one can be used");
            return validName = true;
          });
          //console.log(validName);
      }); 
    } 
  if (!validName) {
    nameRequest();
  }
  }

  $scope.addAliasName = function() {
    var name = $scope.color + " " + $scope.adjective + " " + $scope.animal;
    console.log(alias);
    console.log(name);
    $.ajax({
          url: "api/alias/" + name,
          dataType: "json",
          async: false,
          data: name,
          type: 'PUT'
          // error: ajaxError
        });
  }
  
}]);

appControllers.controller('DevController', ['$scope', '$http', function($scope, $http) {
  $http.get('json-gen/mentors25.json').success(function(data) {
    $scope.mentors = data;
    console.log($scope.mentors);
  }).
  error(function(data, status, headers, config) {
    // called asynchronously if an error occurs
    // or server returns response with an error status.
    console.log("Error getting userData");
  });
  
  $scope.postMentors = function() {
    console.log("length: " + $scope.mentors.length);
    $.ajax({
      url: "api/genFauxMentors",
      dataType: "json",
      async: true,
      data: {'mentors': $scope.mentors}, //$scope.mentors
      type: 'POST',
      success: function(data, textStatus, jqXHR) {
        console.log("Posted Mentors");
      }
      //error: $scope.ajaxError
    });
  }
$scope.deleteMentors = function() {
  $.ajax({
      url: "api/deleteMentors",
      async: true,
      type: 'PUT',
      success: function(data, textStatus, jqXHR) {
        console.log("Deleted Mentors");
      }
      //error: $scope.ajaxError
    });
}
$scope.reset = function() {
    $.ajax({
      url: "api/resetUser",
      dataType: "json",
      async: true,
      success: function(result) {
        //data = result;
      },
      type: 'GET'
      // error: ajaxError
    }); 
  }

}]);


// appControllers.directive('accessibleForm', function () {
//     return {
//         restrict: 'A',
//         link: function (scope, elem) {

//             // set up event handler on the form element
//             elem.on('submit', function () {

//                 // find the first invalid element
//                 var firstInvalid = angular.element(
//                     elem[0].querySelector('.ng-invalid'))[0];

//                 // if we find one, set focus
//                 if (firstInvalid) {
//                     firstInvalid.focus();
//                 }
//             });
//         }
//     };

// });
