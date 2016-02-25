var myApp = angular.module('myApp', [
  'ngRoute',
  'appControllers',
  'iso.directives'
]);

// myApp.factory('Auth', function($http, $q, $window) {
//   var userInfo;

//   function login() {
//     var deferred = $q.defer();

//     $.get('api/welcome')
//     .success(function(data) {
//         // $scope.user = data;
//         // $scope.userType = data['userType'];
//         // $scope.$parent.username = data['username'];
//         deferred.resolve();
//     })
//     .error(function(error) {
//         deferred.reject();
//     });

//     return deferred.promise;
//   }

//   return {
//     login: login
//   };
  
// });

var checkLoggedOut = ['$q', '$timeout', '$http', '$location', '$rootScope', function($q, $timeout, $http, $location, $rootScope){
  deferred = $q.defer();
    $.get('api/user').success(function(user){
      if (user){
        $timeout(deferred.resolve)
      }
      else {
        $timeout(deferred.reject)
        $location.url('/welcome')
      }
    }).error(function(){
      $timeout(deferred.reject)
      $location.url('/welcome')
    })

  return deferred.promise;
   
}];

myApp.config(['$routeProvider', function($routeProvider) {
  $routeProvider.
  when('/welcome', {
    templateUrl: 'partials/welcome.html',
    controller: 'WelcomeController'
  }).
  when('/user-profile', {
    templateUrl: 'partials/user-profile.html',
    controller: 'UserProfileController'
  }).
  when('/register', {
    templateUrl: 'partials/register.html',
    controller: 'RegisterController'
  }).
  when('/menteeReg', {
    templateUrl: 'partials/mentee-reg.html',
    controller: 'RegisterMenteeController'//change to Mentee when
  }).
  when('/mentorAgreement', {
    templateUrl: 'partials/mentor-user-agreement.html',
    controller: 'MentorUserAgreementController'
  }).
  when('/mentorReg', {
    templateUrl: 'partials/mentor-reg.html',
    controller: 'RegisterMentorController'
  }).
  when('/mentorAlias', {
    templateUrl: 'partials/mentor-alias.html',
    controller: 'MentorAliasController'
  }).
  when('/homescreen', {
    templateUrl: 'partials/homescreen.html',
    controller: 'UserController',
    resolve: checkLoggedOut
  }).
  when('/searchmentors', {
    templateUrl: 'partials/searchmentors.html',
    controller: 'SearchController'
  }).
  when('/wishlist', {
    templateUrl: 'partials/wishlist.html',
    controller: 'WishListController'
  }).
  when('/editProfile', {
    templateUrl: 'partials/edit-profile.html',
    controller: 'EditProfileController'
  }).
  when('/list', {
    templateUrl: 'partials/list.html',
    controller: 'ListController'
  }).
  when('/details/:itemId', {
    templateUrl: 'partials/details.html',
    controller: 'DetailsController'
  }).
  when('/loading', {
    templateUrl: 'partials/loading.html',
    controller: 'LoadingController'
  }).
  when('/dev', {
    templateUrl: 'partials/dev.html',
    controller: 'DevController'
  }).
  when('/requestingPeriod', {
    templateUrl: 'partials/requesting-period.html',
    controller: 'RequestingPeriodController'
  }).
  when('/viewMatches', {
    templateUrl: 'partials/view-matches.html',
    controller: 'ViewMatchesController'
  }).
  when('/approveMentors', {
    templateUrl: 'partials/approve-mentors.html',
    controller: 'ApproveMentorController'
  }).
  when('/setMentorMax', {
    templateUrl: 'partials/set-mentor-max.html',
    controller: 'SetMentorMaxController'
  }).
  when('/contact', {
    templateUrl: 'partials/contact.html',
    controller: 'ContactController'
  }).
  when('/logout', {
    templateUrl: 'partials/logout.html',
    controller: 'LogoutController'
  }).
  otherwise({
    redirectTo: '/homescreen'
  });

}]);


myApp.factory('UserInfoService', ['$q','$http','$location',function($q,$http,$location) {
  var userObj = {};

   userObj.comms = [{
        id: 1,
        name: 'Phone'
    }, {
        id: 2,
        name: 'Email'
    }];

   userObj.yesno = [{
      id:1,
      name: 'Yes',
      value: 1
    }, {
      id:2, 
      name: 'No',
      value: 0
    }];

  userObj.genders = [{
      id: 1,
      name: 'Female'
  }, {
      id: 2,
      name: 'Male'
  }];

  userObj.ethnicities = [{
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

  userObj.breadthTracks = [{
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

  userObj.dfocusVals = [{
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


  userObj.honorPrograms = [{
    id:1,
    name: 'Presidents Scholarship Program'
  }, {
    id:2,
    name: 'Honors Program'
  }, {
    id:3,
    name: 'Grand Challenges'
  }];

  userObj.internationalPrograms = [{
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

  userObj.bmeOrganizations = [{
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

  userObj.menteeMentorOrgs =[{
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

  userObj.tutorTeachPrograms = [{
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

  userObj.bmeAcademicPrograms = [{
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

  userObj.internationalPrograms = [{
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

  userObj.carrerDevPrograms = [{
    id:1,
    name:'Co-op'
  }, {
    id:2, 
    name:'Internship'
  }, {
    id:1,
    name:'Shadowing in a Medical Environment'
  }];

  userObj.postGradPlans = [{
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

  var formUI = function() {
    $('.ui.form')
    .form({
      fname: {
        identifier  : 'fname',
        rules: [
          {
            type   : 'empty',
            prompt : 'Please enter your first name'
          }
        ]
      },
      lname: {
        identifier  : 'lname',
        rules: [
          {
            type   : 'empty',
            prompt : 'Please enter your last name'
          }
        ]
      },
      email: {
        identifier  : 'email',
        rules: [
          {
            type   : 'empty',
            prompt : 'Please enter your email'
          },{
            type: 'email',
            prompt: 'Please enter a valid email'
          }
        ]
      },
      prefComm: {
        identifier  : 'prefComm',
        rules: [
          {
            type   : 'empty',
            prompt : 'Please enter your preferred communication method'
          }
        ]
      }
      // other_major: {
      //   identifier: 'other_major',
      //   rules: [
      //     {
      //       type: 'empty',
      //       prompt: 'Please specify your other major'
      //     }
      //   ]
      // }
    },
    {
      inline: true,
      on: 'blur',
      transition: 'fade down', 
    });
  };

  var wrapMenteeData = function(form) {
    var data = {
     'fname': form.fname,
     'lname': form.lname,
     'email': form.email,
     'phone':form.phone,
     'pref_comm': form.prefComm,
     'dfocus': form.dfocus,
     'dfocusother': form.dfocusother,
     'international_student': form.international_student,
     'transfer_from_within': form.transfer_from_within,
     'prev_major': form.prev_major,
     'transfer_from_outside': form.transfer_from_outside,
     'institution_name': form.institution_name,
     'international_student': form.international_student,
     'expec_graduation': form.expec_graduation,
     'other_major': form.other_major,
     'breadth_track': form.breadth_track,
     'undergrad_research': form.undergrad_research,
     'bme_academ_exp': form.bme_academ_exp,
     'bme_organization': form.bme_organization,
     'tutor_teacher_program': form.tutor_teacher_program,
     'international_experience': form.international_experience,
     'career_dev_program': form.career_dev_program,
     'post_grad_plan': form.post_grad_plan,
     'post_grad_plan_desc': form.post_grad_plan_desc,
     'personal_hobby': form.personal_hobby
    };
    return data;
  };

  var wrapMentorData = function(form) {
    var data = {
     'fname': form.fname,
     'lname': form.lname,
     'email': form.email,
     'phone':form.phone,
     'pref_communication': form.prefComm,
     'dfocus': form.dfocus,
     'dfocusother': form.dfocusother,
     'gender': form.gender,
     'ethnicity': form.ethnicity,
     'live_before_tech': form.live_before_tech,
     'live_on_campus': form.live_on_campus,
     'first_gen_college_student': form.first_gen_college_student,
     'transfer_from_within': form.transfer_from_within,  
     'prev_major': form.prev_major,
     'transfer_from_outside': form.transfer_from_outside,
     'institution_name': form.institution_name,
     'international_student': form.international_student,
     'home_country': form.home_country,
     'expec_graduation': form.expec_graduation,
     'honor_program': form.honor_program,
     'other_major': form.other_major,
     'breadth_track': form.breadth_track,
     'undergrad_research': form.undergrad_research,
     'undergrad_research_desc':form.undergrad_research_desc,
     'other_organization1':form.other_organization1,
     'other_organization2':form.other_organization2,
     'other_organization3':form.other_organization3, 
     'bme_organization': form.bme_organization,
     'bme_org_other': form.bme_org_other,
     'mm_org': form.mm_org,
     'mm_org_other': form.mm_org_other,
     'tutor_teacher_program': form.tutor_teacher_program,
     'tutor_teacher_program_other': form.tutor_teacher_program_other,
     'bme_academ_exp': form.bme_academ_exp,
     'bme_academ_exp_other': form.bme_academ_exp_other,
     'international_experience': form.international_experience,
     'international_experience_other':form.international_experience_other,
     'career_dev_program': form.career_dev_program,
     'career_dev_program_other': form.career_dev_program_other,
     'post_grad_plan': form.post_grad_plan,
     'post_grad_plan_desc': form.post_grad_plan_desc,
     'personal_hobby': form.personal_hobby
    }

    return data;
  };

  var editProfileData = function(form) {
    var data = {
     'fname': form.first_name,
     'lname': form.last_name,
     'email': form.email,
     'phone':form.phone_num,
     'pref_communication': form.pref_communication,
     'dfocus': form.depth_focus,
     'dfocusother': form.depth_focus_other,
     'gender': form.gender,
     'ethnicity': form.ethnicity,
     'live_before_tech': form.live_before_tech,
     'live_on_campus': form.live_on_campus,
     'first_gen_college_student': form.first_gen_college_student,
     'transfer_from_within': form.transfer_from_within,  
     'prev_major': form.prev_major,
     'transfer_from_outside': form.transfer_from_outside,
     'institution_name': form.institution_name,
     'international_student': form.international_student,
     'home_country': form.home_country,
     'expec_graduation': form.expec_graduation,
     'honor_program': form.honor_program,
     'other_major': form.other_major,
     'breadth_track': form.breadth_track,
     'undergrad_research': form.undergrad_research,
     'undergrad_research_desc':form.undergrad_research_desc,
     'other_organization1':form.organization1,
     'other_organization2':form.organization2,
     'other_organization3':form.organization3, 
     'bme_organization': form.bme_organization,
     'bme_org_other': form.bme_org_other,
     'mm_org': form.mm_org,
     'mm_org_other': form.mm_org_other,
     'tutor_teacher_program': form.tutor_teacher_program,
     'tutor_teacher_program_other': form.tutor_teacher_program_other,
     'bme_academ_exp': form.bme_academ_exp,
     'bme_academ_exp_other': form.bme_academ_exp_other,
     'international_experience': form.international_experience,
     'international_experience_other':form.international_experience_other,
     'career_dev_program': form.career_dev_program,
     'career_dev_program_other': form.career_dev_program_other,
     'post_grad_plan': form.post_grad_plan,
     'post_grad_plan_desc': form.post_grad_plan_desc,
     'personal_hobby': form.personal_hobby
    }

    return data;
  }

  var data_expand = function(data) {
    // data.breadth_track = userObj.breadthTracks;
    data.breadth_track = [];
    data.bme_academ_exp = [];
    data.bme_organization = [];
    data.career_dev_program = [];
    data.ethnicity = [];
    data.international_experience = [];
    data.mm_org = [];
    data.tutor_teacher_program = [];
    data.honor_program = [];
    var i;
    for(i = 1;i < 7;i++) {
      if(data['program'+i] != null && data['program'+i] != '') {
        data.honor_program.push(data['program'+i]);
      }
      if(data['bme_academ_exp'+i] != null && data['bme_academ_exp'+i] != '') {
        data.bme_academ_exp.push(data['bme_academ_exp'+i]);
      }
      if(data['bme_org'+i] != null && data['bme_org'+i] != '') {
        data.bme_organization.push(data['bme_org'+i]);
      }
      if(data['career_dev_program'+i] != null && data['career_dev_program'+i] != '') {
        data.career_dev_program.push(data['career_dev_program'+i]);
      }
      if(data['ethnicity'+i] != null && data['ethnicity'+i] != '') {
        data.ethnicity.push(data['ethnicity'+i]);
      }
      if(data['international_experience'+i] != null && data['international_experience'+i] != '') {
        data.international_experience.push(data['international_experience'+i]);
      }
      if(data['mm_org'+i] != null && data['mm_org'+i] != '') {
        data.mm_org.push(data['mm_org'+i]);
      }
      if(data['tutor_teacher_program'+i] != null && data['tutor_teacher_program'+i] != '') {
        data.tutor_teacher_program.push(data['tutor_teacher_program'+i]);
      }
    }

    if(data.transfer_from_outside == 0 && data.transfer_from_within == 0) data.non_transfer = -1;

    data.bts = userObj.breadthTracks;
    var bt = data.breadth_tracks.split(',');
    var btd = data.breadth_track_descs.split(',');
    for (i = 0;i < bt.length;i++) {
      for(var j = 0;j < data.bts.length;j++) {
        if(data.bts[j].name == bt[i]) {
          data.bts[j].desc = btd[i];
          data.bts[j].display = 1;
          data.breadth_track.push(data.bts[j]);
        }
      }
    }

    data.majorHelper = data.other_major == '' ? 'No' : 'Yes';
    return data;
  }

  var Update_Description = function(bts,userInfo) {
    for (var j = 0;j < userInfo.breadthTracks.length;j++) {
      for (var i = 0;i < bts.length;i++) {
        if(userInfo.breadthTracks[j].name == bts[i].name && bts[i].desc != '') {
           userInfo.breadthTracks[j].desc = bts[i].desc;
           userInfo.breadthTracks[j].display = 1;
        }
      } 
    }
     
    return userInfo;
  }

  return {
    "userInfo":userObj,
    "formui":formUI,
    "wrapMenteeData":wrapMenteeData,
    "wrapMentorData":wrapMentorData,
    "data_expand":data_expand,
    "update_description": Update_Description,
    "editprofiledata":editProfileData
  };
}]);
