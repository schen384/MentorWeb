var appControllers = angular.module('appControllers', ['ngAnimate', 'ngResource']);

appControllers.controller('mainController', ['$scope', '$http', '$location', function($scope, $http, $location) {
  $scope.go = function(path) {
    $location.path(path);
    $location.reload(true);
  };
}]);

appControllers.controller('HeaderController', ['$scope', '$http', '$location', function($scope, $http, $location) {
  $scope.go = function(path) {
    $location.path(path);
    // window.location.reload(true);
  };

  // if($window.location == "\welcome" || $window.location == "\register" || $window.location == "\menteeReg"){
    $scope.user = {none: 1,
    mentee: 0,
    mentor: 0,
    admin: 0};
  
  // $.get("api/user", function (data) {
  //       data = data;//$data = data;//$('#hello').tmpl(data).appendTo("#hello");
  //       console.log("data: " , data);
  // });
var data = {};
if(window.location.href.indexOf("welcome") > -1 || window.location.href.indexOf("register") > -1){
  console.log("no get sent");
  $scope.user = {none: 1,
    mentee: 0,
    mentor: 0,
    admin: 0};
} else {
  $.ajax({
      url: "api/user",
      dataType: "json",
      async: false,
      success: function(result) {
        data = result;
      },
      type: 'GET'
      // error: ajaxError
    }); 
  if(data["Mentor"]) {
    $scope.user.none = 0;
    $scope.user.mentor = 1;
  }
  if(data["Mentee"]) {
    $scope.user.none = 0;
    $scope.user.mentee = 1;
  }
  if(data["Admin"]) {
    $scope.user.none = 0;
    $scope.user.admin = 1;
  }
}
}]);

appControllers.controller('EditProfileController', ['$scope', '$http', '$location', function($scope, $http, $location) {
  $scope.go = function(path) {
    $location.path(path);
    $location.reload(true);
  };
  var data = {};
  $.ajax({
    url: "api/user",
    dataType: "json",
    async: false,
    success: function(result) {
      data = result;
    },
    type: 'GET'
    // error: ajaxError
  }); 
  if(data["Mentor"]) {
    $scope.viewMentorForm = 1;
    $scope.viewMenteeForm = 0;
  }
  if(data["Mentee"]) {
    $scope.viewMenteeForm = 1;
    $scope.viewMentorForm = 0;
  }

}]);

appControllers.controller('WelcomeController', ['$scope', '$http', '$location', function($scope, $http, $location) {
  // window.location.reload(true);

  $scope.go = function() {
    window.location.replace("https://login.gatech.edu/cas/login?service=http%3A%2F%2Fdev.m.gatech.edu%2Fd%2Faarrowood3%2Fw%2FMentorAngular%2Fcontent%2F")
  };
}]);

appControllers.controller('ForkController', ['$scope', '$http', function($scope, $http) {

}]);

appControllers.controller('UserController', ['$scope', '$http', function($scope, $http) {
  $.get('api/welcome').success(function(data) {
    $scope.user = data;
    $scope.userType = data['userType'];
    console.log(data);
  });
}]);

appControllers.controller('LoadingController', ['$scope', '$http', function($scope, $http) {
  // $http.get('http://dev.m.gatech.edu/d/mosborne8/w/mentoringweb/content/api/welcome').success(function(data) {
  //   $scope.user = data['username'];
  //   $scope.userType = data['userType'];
  // });
}]);

appControllers.controller('HomeController', ['$scope', '$http', '$location', function($scope, $http, $location) {
  $scope.go = function( path ) {
    $location.path(path);
    $location.reload(true);
  }; 

  $scope.user = {type:[],
    none: 1,
    mentee: 0,
    mentor: 0,
    admin: 0,
    id: ''};
  var data = {};
  
  // $.get("api/user", function (data) {
  //       data = data;//$data = data;//$('#hello').tmpl(data).appendTo("#hello");
  //       console.log("data: " , data);
  // });
  $.ajax({
      url: "api/user",
      dataType: "json",
      async: false,
      success: function(result) {
        data = result;
      },
      type: 'GET'
      // error: ajaxError
    }); 
  console.log("hoescreen user:");
  console.log(data);
  $scope.user.name = data["Name"];
  $scope.user.id = data["Id"];

  if(data["Mentor"]) {
    $scope.user.type.push("Mentor");
  }
  if(data["Mentee"]) {
    $scope.user.type.push("Mentee");

    // $.ajax({
    //   url: "api/mentee",
    //   dataType: "json",
    //   async: false,
    //   success: function(result) {
    //     data = result;
    //   },
    //   type: 'GET'
    //   // error: ajaxError
    // }); 

  }
  if(data["Admin"]) {
    $scope.user.type.push("Admin");
  }

}]);

appControllers.controller('SearchController', ['$scope', '$http', function($scope, $http) {
  $('.ui.checkbox').checkbox();
  $('.ui.accordion').accordion();

  $scope.$parent.wishList = ($scope.$parent.wishList || []);

  $http.get('json-gen/users120.json').success(function(data) {
    $scope.userData = data;
    $scope.miniProfileData = $scope.userData[0];
    $scope.wishButton = {};
    $scope.renderButton($scope.miniProfileData.favorited);
    $scope.refreshUI();
  }).
  error(function(data, status, headers, config) {
    // called asynchronously if an error occurs
    // or server returns response with an error status.
    console.log("Error getting userData");
  });

  $scope.miniProfileSet = function(user) {
    //console.log("yo ");
    //console.log(user);
    $scope.miniProfileData = user;
    $scope.renderButton($scope.miniProfileData.favorited);
  }
  $scope.addToWishlist = function() {
    $scope.miniProfileData.favorited = "favorited";
    $scope.renderButton($scope.miniProfileData.favorited);
    $scope.$parent.wishList.push($scope.miniProfileData);
  }
  $scope.removeFromWishlist = function() {
    $scope.miniProfileData.favorited = "";
    $scope.renderButton($scope.miniProfileData.favorited);
    $scope.$parent.wishList.splice($.inArray($scope.miniProfileData, $scope.$parent.wishList), 1 );
  }
  $scope.refreshUI = function() {
    $scope.userData.forEach(function(element) {
      var user = element;
      $scope.$parent.wishList.forEach(function(element) {
        if (user.uid === element.uid) { //JSON.stringify(user) === JSON.stringify(element)
          user.favorited = "favorited";
        }
      });
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
      console.log("wishButton text: " + $scope.wishButton);
    }
  }
}]);

appControllers.controller('WishListController', ['$scope', '$http', function($scope, $http) {
  $scope.userData = $scope.$parent.wishList;
  if ($scope.userData) {
    $scope.miniProfileData = $scope.userData[0];
  }
  console.log('yo');
  $scope.miniProfileSet = function(user) {
    $scope.miniProfileData = user;
  }
  $scope.notification = function() {
    $('#mentor-note').dimmer('toggle');
  }
  $scope.removeFromWishlist = function() {
    $scope.miniProfileData.favorited = "";
    //$scope.userData.splice($.inArray($scope.miniProfileData, $scope.userData), 1 );
    $.each($scope.userData, function(i){
      if($scope.userData[i].uid === $scope.miniProfileData.uid) {
        console.log("splice");
        console.log($scope.userData[i].uid);
        console.log($scope.miniProfileData.uid);
        $scope.userData.splice(i,1);
        return false;
      }
    });
    $scope.$parent.wishList = $scope.userData;

    //$scope.$parent.wishList.splice($.inArray($scope.miniProfileData, $scope.$parent.wishList), 1 );
  }
  $scope.chooseMentor = function() {
    $scope.$parent.myMentor = $scope.miniProfileData;
    $scope.myMentor = $scope.$parent.myMentor;
    console.log("chooseMentor");
    $scope.go('/user-profile');
  }
  $scope.refreshUI = function() {
    $scope.userData.forEach(function(element) {
      var user = element;
      $scope.$parent.wishList.forEach(function(element) {
        if (user.uid === element.uid) { //JSON.stringify(user) === JSON.stringify(element)
          user.favorited = "favorited";
        }
      });
    });
  }
  if ($scope.userData) {
    //$scope.refreshUI();
  }
}]);

appControllers.controller('UserProfileController', ['$scope', '$http', '$location', function($scope, $http, $location) {
  $scope.myMentor = $scope.$parent.myMentor;
}]);

appControllers.controller('RegisterController', ['$scope', '$http', '$location', function($scope, $http, $location) {
  $scope.go = function( path ) {
    $location.path(path);
    window.location.reload(true);
  };
}]);

appControllers.controller('RegisterMenteeController', ['$scope', '$http', '$filter', '$location', function($scope, $http, $filter, $location) {
  $('.ui.radio.checkbox').checkbox();
  $('.ui.checkbox').checkbox();
  $scope.form = { 
      dfocus: '', 
      breadth_track:[],
      bme_organization: [],
      tutor_teacher_program: [],
      bme_academ_exp: [],
      international_experience: [],
      career_dev_program: []
  };

   $scope.comms = [{
        id: 1,
        name: 'Phone'
    }, {
        id: 2,
        name: 'Email'
    }];

   $scope.yesno = [{
      id:1,
      name: 'Yes',
      value: 1
    }, {
      id:2, 
      name: 'No',
      value: 0
    }];

  $scope.breadthTracks = [{
    id:1,
    name:'Pre-health',
    desc: ''
  }, {
    id:2,
    name:'Research Option',
    desc:''
  }, {
    id:3, 
    name:'Minor',
    desc:''
  }, {
    id:4, 
    name:'Certificate',
    desc:''
  }, {
    id:5, 
    name:'Not Sure',
    desc:''
  }];

  $scope.dfocusVals = [{
    id:1,
    name: "Neuroengineering"
  }, {
    id:2,
    name: "Cardiovascular Systems"
  }, {
    id:3, 
    name: "Biomechanics"
  }, {
    id:4,
    name: "Biomaterials/Tissue Engineering"
  }, {
    id:5, 
    name: "Medical Imaging"
  }, {
    id:6,
    name: "Some of Everything"
  }, {
    id:7,
    name: "Other",
    other: ""
  }];

  $scope.internationalPrograms = [{
    id:1, 
    name:'International Plan'
  }, {
    id:2, 
    name:'Study Abroad'
  }, {
    id:3, 
    name:'Work Abroad'
  }, {
    id:4,
    name:'Research Abroad'
  }, {
    id:5, 
    name:'Volunteer Abroad'
  }];

  $scope.bmeOrganizations = [{
    id:1,
    name:'Alpha Eta Mu (AEMB)'
  }, {
    id:2,
    name:'Biomedical Engineering Society (BMES)'
  }, {
    id:3,
    name:'Biomedical Research & Opportunities Society (BROS)'
  }, {
    id:4, 
    name:'BMED Futures'
  }, {
    id:5, 
    name:'Engineering World Health (EWH)'
  }, {
    id:6, 
    name:'Medical Device Entrepreneurship Association (MDEA)'
  }, {
    id:7, 
    name:'Pioneer'
  }];

  $scope.tutorTeachPrograms = [{
    id:1, 
    name:'PLUS Leader (Center for Academic Success)'
  }, {
    id:2, 
    name:'1 On 1 Tutoring'
  }, {
    id:3, 
    name:'Tutoring in BME with a Student Organization'
  }, {
    id:4, 
    name:'Ad Hoc Tutoring (That You Arranged on Your Own)'
  }, {
    id:5,
    name:'BMED 1300 Co-Facilitator'
  }, {
    id:6, 
    name:'Undergraduate Grader or Teaching Assistant for a BME Course'
  }];

  $scope.bmeAcademicPrograms = [{
    id:1,
    name:'Inventure Prize'
  }, {
    id:2,
    name:'Design Expo'
  }, {
    id:3,
    name:'Multidisciplinary Capstone Design Course'
  }, {
    id:4, 
    name:'The Clinical Observation and Design Experience (CODE) Course  (BMED 4813)'
  }];

  $scope.internationalPrograms = [{
    id:1, 
    name:'International Plan'
  }, {
    id:2, 
    name:'Study Abroad'
  }, {
    id:3, 
    name:'Work Abroad'
  }, {
    id:4,
    name:'Research Abroad'
  }, {
    id:5, 
    name:'Volunteer Abroad'
  }];

  $scope.carrerDevPrograms = [{
    id:1,
    name:'Co-op'
  }, {
    id:2, 
    name:'Internship'
  }, {
    id:1,
    name:'Shadowing in a Medical Environment'
  }];

  $scope.postGradPlans = [{
    id:1,
    name:'Industry'
  }, {
    id:2,
    name:'Pursue Professional Degree in Healthcare'
  }, {
    id:3, 
    name:'Graduate School'
  }, {
    id:4, 
    name:'Entrepreneur'
  }, {
    id:5,
    name:'I\'m Not Sure'
  }, {
    id:6,
    name:'Other'
  }];

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
    if (value && attr == "transfer_from_within") {
      $scope.form.transfer_from_outside = 0;
      $scope.form.institution_name = "";
    } else if (value && attr == "transfer_from_outside") {
      $scope.form.transfer_from_within = 0;
      $scope.form.prev_major = "";
    }
  }

  $scope.addMentee = function() {
    $.ajax({
      url: "api/mentee",
      dataType: "json",
          async: false,
      data: {'fname': $scope.form.fname,
             'lname': $scope.form.lname,
             'email': $scope.form.email,
             'phone':$scope.form.phone,
             'pref_comm': $scope.form.prefComm,
             'dfocus': $scope.form.dfocus,
             'dfocusother': $scope.form.dfocusother,
             'international_student': $scope.form.international_student,
             'freshman': $scope.form.freshman,
             'transfer_from_within': $scope.form.transfer_from_within,
             'prev_major': $scope.form.prev_major,
             'transfer_from_outside': $scope.form.transfer_from_outside,
             'institution_name': $scope.form.institution_name,
             'international_student': $scope.form.international_student,
             'expec_graduation': $scope.form.expec_graduation,
             '$other_major': $scope.form.other_major,
             'breadth_track': $scope.form.breadth_track,
             'undergrad_research': $scope.form.undergrad_research,
             'bme_academ_exp': $scope.form.bme_academ_exp,
             'bme_organization': $scope.form.bme_organization,
             'tutor_teacher_program': $scope.form.tutor_teacher_program,
             'international_experience': $scope.form.international_experience,
             'career_dev_program': $scope.form.career_dev_program,
             'career_dev_program_desc': $scope.form.career_dev_program_desc,
             'post_grad_plan': $scope.form.post_grad_plan,
             'post_grad_plan_desc': $scope.form.post_grad_plan_desc,
             'personal_hobby': $scope.form.personal_hobby
            },
      type: 'POST'
      // error: ajaxError
    }); 
  };

// $.post("api/mentee",
//             {'fname': $scope.form.fname,
//              'lname': $scope.form.lname,
//              'email': $scope.form.email,
//              'phone':$scope.form.phone,
//              'pref_comm': $scope.form.prefComm,
//              'dfocus': $scope.form.dfocus,
//              'dfocusother': $scope.form.dfocusother,
//              'international_student': $scope.form.international_student,
//              'freshman': $scope.form.freshman,
//              'transfer_from_within': $scope.form.transfer_from_within,
//              'prev_major': $scope.form.prev_major,
//              'transfer_from_outside': $scope.form.transfer_from_outside,
//              'institution_name': $scope.form.institution_name,
//              'expec_graduation': $scope.form.expec_graduation,
//              '$other_major': $scope.form.other_major,
//              'breadth_track': $scope.form.breadth_track,
//              'undergrad_research': $scope.form.undergrad_research,
//              'bme_academ_exp': $scope.form.bme_academ_exp,
//              'bme_organization': $scope.form.bme_organization,
//              'tutor_teacher_program': $scope.form.tutor_teacher_program,
//              'international_experience': $scope.form.international_experience,
//              'career_dev_program': $scope.form.career_dev_program,
//              'career_dev_program_desc': $scope.form.career_dev_program_desc,
//              'post_grad_plan': $scope.form.post_grad_plan,
//              'post_grad_plan_desc': $scope.form.post_grad_plan_desc,
//              'personal_hobby': $scope.form.personal_hobby
//             }
//           );
//   };

   $scope.go = function( path ) {
    $location.path(path);
    window.location.reload(true);
  };

}]);


appControllers.controller('RegisterMentorController', ['$scope', '$http', '$filter', '$location', 
  function($scope, $http, $filter, $location) {
  // $('.ui.dropdown').dropdown();
  $('.ui.radio.checkbox').checkbox();
  $('.ui.checkbox').checkbox();

  $scope.form = { 
      dfocus: null, 
      ethnicity: [],
      honor_program: [],
      breadth_track:[],
      bme_organization: [],
      mentee_mentor_organization: [],
      tutor_teacher_program: [],
      bme_academ_exp: [],
      international_experience: [],
      career_dev_program: []
  };

  $scope.yesno = [{
      id:1,
      name: 'Yes',
      value: 1
  }, {
      id:2, 
      name: 'No',
      value: 0
  }];

  $scope.comms = [{
        id: 1,
        name: 'Phone'
    }, {
        id: 2,
        name: 'Email'
    }];

  $scope.genders = [{
      id: 1,
      name: 'Female'
  }, {
      id: 2,
      name: 'Male'
  }];

  $scope.ethnicities = [{
      id: 1,
      name: 'American Indian or Alaskan Native'
  }, {
      id: 2,
      name: 'Asian or Pacific Islander'
  }, {
      id: 3,
      name: 'Black or African American'
  }, {
      id: 4,
      name: 'Hispanic or Latino'
  }, {
      id: 5,
      name: 'White/Caucasian'
  }];

  $scope.dfocusVals = [{
    id:1,
    name: "Neuroengineering"
  }, {
    id:2,
    name: "Cardiovascular Systems"
  }, {
    id:3, 
    name: "Biomechanics"
  }, {
    id:4,
    name: "Biomaterials/Tissue Engineering"
  }, {
    id:5, 
    name: "Medical Imaging"
  }, {
    id:6,
    name: "Some of Everything"
  }, {
    id:7,
    name: "Other",
    other: ""
  }];

  $scope.breadthTracks = [{
    id:1,
    name:'Pre-health',
    desc: ''
  }, {
    id:2,
    name:'Research Option',
    desc:''
  }, {
    id:3, 
    name:'Minor',
    desc:''
  }, {
    id:4, 
    name:'Certificate',
    desc:''
  }, {
    id:5, 
    name:'Not Sure',
    desc:''
  }];

  $scope.honorPrograms = [{
    id:1,
    name: 'President\'s Scholarship Program'
  }, {
    id:2,
    name: 'Honors Program'
  }, {
    id:3,
    name: 'Grand Challenges'
  }];

  $scope.bmeOrganizations = [{
    id:1,
    name:'Alpha Eta Mu (AEMB)'
  }, {
    id:2,
    name:'Biomedical Engineering Society (BMES)'
  }, {
    id:3,
    name:'Biomedical Research & Opportunities Society (BROS)'
  }, {
    id:4, 
    name:'BMED Futures'
  }, {
    id:5, 
    name:'Engineering World Health (EWH)'
  }, {
    id:6, 
    name:'Medical Device Entrepreneurship Association (MDEA)'
  }, {
    id:7, 
    name:'Pioneer'
  }];

  $scope.menteeMentorOrgs =[{
    id:1,
    name:'Mentor Jackets'
  }, {
    id:2,
    name:'M&M Mentoring'
  }, {
    id:3, 
    name:'Ceismic Academic Mentoring'
  }, {
    id:4, 
    name:'Office of Minority Education (OMED) Mentor'
  }, {
    id:5,
    name:'BMED 1000 Mentor'
  }];

  $scope.tutorTeachPrograms = [{
    id:1, 
    name:'PLUS Leader (Center for Academic Success)'
  }, {
    id:2, 
    name:'1 On 1 Tutoring'
  }, {
    id:3, 
    name:'Tutoring in BME with a Student Organization'
  }, {
    id:4, 
    name:'Ad Hoc Tutoring (That You Arranged on Your Own)'
  }, {
    id:5,
    name:'BMED 1300 Co-Facilitator'
  }, {
    id:6, 
    name:'Undergraduate Grader or Teaching Assistant for a BME Course'
  }];

  $scope.bmeAcademicPrograms = [{
    id:1,
    name:'Inventure Prize'
  }, {
    id:2,
    name:'Design Expo'
  }, {
    id:3,
    name:'Multidisciplinary Capstone Design Course'
  }, {
    id:4, 
    name:'The Clinical Observation and Design Experience (CODE) Course  (BMED 4813)'
  }];

  $scope.internationalPrograms = [{
    id:1, 
    name:'International Plan'
  }, {
    id:2, 
    name:'Study Abroad'
  }, {
    id:3, 
    name:'Work Abroad'
  }, {
    id:4,
    name:'Research Abroad'
  }, {
    id:5, 
    name:'Volunteer Abroad'
  }];

  $scope.carrerDevPrograms = [{
    id:1,
    name:'Co-op'
  }, {
    id:2, 
    name:'Internship'
  }, {
    id:1,
    name:'Shadowing in a Medical Environment'
  }];

  $scope.postGradPlans = [{
    id:1,
    name:'Industry'
  }, {
    id:2,
    name:'Pursue Professional Degree in Healthcare'
  }, {
    id:3, 
    name:'Graduate School'
  }, {
    id:4, 
    name:'Entrepreneur'
  }, {
    id:5,
    name:'I\'m Not Sure'
  }, {
    id:6,
    name:'Other'
  }];

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
    if (value && attr == "transfer_from_within") {
      $scope.form.transfer_from_outside = 0;
      $scope.form.institution_name = null;
    } else if (value && attr == "transfer_from_outside") {
      $scope.form.transfer_from_within = 0;
      $scope.form.prev_major = null;
    }
    if (value !="Yes" && attr == "majorHelper") {
      $scope.form.other_major = null;
    }
    if (!value && attr == "undergrad_research") {
      $scope.form.undergrad_research_desc = null;
    } 
  }

  $scope.addMentor = function() {
    $.ajax({
      url: "api/mentor",
      dataType: "json",
      async: false,
      data: {'fname': $scope.form.fname,
             'lname': $scope.form.lname,
             'email': $scope.form.email,
             'phone':$scope.form.phone,
             'pref_comm': $scope.form.prefComm,
             'dfocus': $scope.form.dfocus,
             'dfocusother': $scope.form.dfocusother,
             'gender': $scope.form.gender,
             'ethnicity': $scope.form.ethnicity,
             'live_before_tech': $scope.form.live_before_tech,
             'live_on_campus': $scope.form.live_on_campus,
             'first_gen_college_student': $scope.form.first_gen_college_student,
             'transfer_from_within': $scope.form.transfer_from_within,
             'prev_major': $scope.form.prev_major,
             'transfer_from_outside': $scope.form.transfer_from_outside,
             'institution_name': $scope.form.institution_name,
             'international_student': $scope.form.international_student,
             'home_country': $scope.form.home_country,
             'expec_graduation': $scope.form.expec_graduation,
             'honor_program': $scope.form.honor_program,
             'other_major': $scope.form.other_major,
             'breadth_track': $scope.form.breadth_track,
             'undergrad_research': $scope.form.undergrad_research,
             'undergrad_research_desc':$scope.form.undergrad_research_desc,
             'other_organization':$scope.form.other_organization, 
             'bme_organization': $scope.form.bme_organization,
             'bme_organization_other': $scope.form.bme_organization_other,
             'mentee_mentor_organization': $scope.form.mentee_mentor_organization,
             'mentee_mentor_organization_other': $scope.form.mentee_mentor_organization_other,
             'tutor_teacher_program': $scope.form.tutor_teacher_program,
             'tutor_teacher_program_desc': $scope.form.tutor_teacher_program_desc,
             'bme_academ_exp': $scope.form.bme_academ_exp,
             'bme_academ_exp_desc': $scope.form.bme_academ_exp_desc,
             'international_experience': $scope.form.international_experience,
             'international_experience_desc':$scope.form.international_experience_desc,
             'career_dev_program': $scope.form.career_dev_program,
             'career_dev_program_desc': $scope.form.career_dev_program_desc,
             'post_grad_plan': $scope.form.post_grad_plan,
             'post_grad_plan_desc': $scope.form.post_grad_plan_desc,
             'personal_hobby': $scope.form.personal_hobby
            },
      type: 'POST'
      // error: ajaxError
    });
  };

   $scope.go = function( path ) {
    $location.path(path);
    window.location.reload(true);
  };
  
}]);

appControllers.controller('MentorAliasController', ['$scope', '$http', '$location', function($scope, $http, $location) {
   
     $scope.generate = function() {
    $.ajax({
      url: "api/mentor",
      dataType: "json",
          async: false,
      data: {'alias': $scope.f 
            },
      type: 'POST'
      // error: ajaxError
    });
  };

   $scope.go = function( path ) {
    $location.path(path);
    window.location.reload(true);
  };

}]);

appControllers.controller('ListController', ['$scope', '$http', function($scope, $http) {
  $http.get('js/data.json').success(function(data) {
    $scope.apps = data;
    $scope.appOrder = 'name';
  });
}]);

appControllers.controller('DetailsController', ['$scope', '$http','$routeParams', function($scope, $http, $routeParams) {
  $http.get('js/data.json').success(function(data) {
    $scope.apps = data;
    $scope.whichItem = $routeParams.itemId;

    if ($routeParams.itemId > 0) {
      $scope.prevItem = Number($routeParams.itemId)-1;
    } else {
      $scope.prevItem = $scope.apps.length-1;
    }

    if ($routeParams.itemId < $scope.apps.length-1) {
      $scope.nextItem = Number($routeParams.itemId)+1;
    } else {
      $scope.nextItem = 0;
    }

  });
}]);


