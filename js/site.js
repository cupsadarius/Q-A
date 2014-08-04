$(document).ready(function () {
    $('#registerForm').bootstrapValidator({
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            firstName: {
                message: 'Your first name is not valid',
                validators: {
                    notEmpty: {
                        message: 'The First Name field is required and cannot be empty'
                    }
                }
            },
            lastName: {
                message: 'Your last name is not valid',
                validators: {
                    notEmpty: {
                        message: 'The Last Name field is required and cannot be empty'
                    }
                }
            },
            username: {
                message: 'The username is not valid',
                validators: {
                    notEmpty: {
                        message: 'The username is required and cannot be empty'
                    },
                    stringLength: {
                        min: 6,
                        max: 30,
                        message: 'The username must be more than 6 and less than 30 characters long'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9]+$/,
                        message: 'The username can only consist of alphabetical and number'
                    },
                    different: {
                        field: 'password',
                        message: 'The username and password cannot be the same as each other'
                    }
                }
            },
            email: {
                validators: {
                    notEmpty: {
                        message: 'The email address is required and cannot be empty'
                    },
                    emailAddress: {
                        message: 'The email address is not a valid'
                    }
                }
            },
            password: {
                validators: {
                    notEmpty: {
                        message: 'The password is required and cannot be empty'
                    },
                    different: {
                        field: 'username',
                        message: 'The password cannot be the same as username'
                    },
                    stringLength: {
                        min: 8,
                        message: 'The password must have at least 8 characters'
                    }
                }
            },
            birthday: {
                validators: {
                    notEmpty: {
                        message: 'The date of birth is required'
                    },
                    date: {
                        format: 'YYYY/MM/DD',
                        message: 'The date of birth is not valid'
                    }
                }
            }
        }
    });
    $('#editUserForm').bootstrapValidator({
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            firstName: {
                message: 'Your first name is not valid',
                validators: {
                    notEmpty: {
                        message: 'The First Name field is required and cannot be empty'
                    }
                }
            },
            lastName: {
                message: 'Your last name is not valid',
                validators: {
                    notEmpty: {
                        message: 'The Last Name field is required and cannot be empty'
                    }
                }
            },
            username: {
                message: 'The username is not valid',
                validators: {
                    notEmpty: {
                        message: 'The username is required and cannot be empty'
                    },
                    stringLength: {
                        min: 6,
                        max: 30,
                        message: 'The username must be more than 6 and less than 30 characters long'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9]+$/,
                        message: 'The username can only consist of alphabetical and number'
                    },
                    different: {
                        field: 'password',
                        message: 'The username and password cannot be the same as each other'
                    }
                }
            },
            email: {
                validators: {
                    notEmpty: {
                        message: 'The email address is required and cannot be empty'
                    },
                    emailAddress: {
                        message: 'The email address is not a valid'
                    }
                }
            },
            current_password:{
                validators: {
                    notEmpty: {
                        message: 'The password is required and cannot be empty'
                    },
                    different: {
                        field: 'username',
                        message: 'The password cannot be the same as username'
                    },
                    stringLength: {
                        min: 8,
                        message: 'The password must have at least 8 characters'
                    }
                }
            },
            password: {
                validators: {
                    different: {
                        field: 'username',
                        message: 'The password cannot be the same as username'
                    },
                    different: {
                        field: 'current_password',
                        message: 'The new password can\'t be the same as the old one'
                    },
                    stringLength: {
                        min: 8,
                        message: 'The password must have at least 8 characters'
                    }
                }
            },
            birthday: {
                validators: {
                    notEmpty: {
                        message: 'The date of birth is required'
                    },
                    date: {
                        format: 'YYYY/MM/DD',
                        message: 'The date of birth is not valid'
                    }
                }
            }
        }
    });
});
var loginDialog = $('#loginDialog');
var registerDialog = $('#registerDialog');
var editUserDialog = $('#editUserDialog');
var addQuestionDialog = $('#addQuestionDialog');
var editQuestionDialog = $('#editQuestionDialog');
var editAnswerDialog = $('#editAnswerDialog');
var base_url = "http://qanda.local"
function loginModal() {
    loginDialog.modal('show');

}
function registerModal() {
    $('#loginForm').on('submit', function () {
        return false;
    });
    loginDialog.modal('hide');
    registerDialog.modal('show');
}

function editUserModal(){
    editUserDialog.modal('show');
}

function addQuestionModal(){
    addQuestionDialog.modal('show');
}

function editQuestionModal(question_id){
    $.getJSON(base_url+'/question/json/'+question_id,function(data){
        $('#editQuestionForm #question').val(data.question);
        $('#editQuestionForm #section option').filter(function(){
            return this.value == data.section_id;
        }).attr('selected',true);
        $('#editQuestionForm #description').html(data.description);
        $('#editQuestionForm #question_id').val(data.id);
    });
    editQuestionDialog.modal('show');
}

function editAnswerModal(answer_id){
    $.getJSON(base_url+'/answer/json/'+answer_id,function(data){
       console.log(data);
       $('#editAnswerForm #title').val(data.title);
       $('#editAnswerForm #answer').html(data.answer);
       $('#editAnswerForm #answer_id').val(data.id);
    });

    editAnswerDialog.modal('show');
}



function search() {
    $('#searchForm').submit();
}
function rateUp(answer_id) {
    $.post(base_url+'/answer/rate', {answer_id: answer_id, rating: 1}, function (data) {
        var rat = data.split(' ');
        $('#up').text(rat[0]);
        $('#down').text(rat[1]);
    }, 'html');
}
function rateDown(answer_id) {
    $.post(base_url+'/answer/rate', {answer_id: answer_id, rating: -1}, function (data) {
        var rat = data.split(' ');
        $('#up').text(rat[0]);
        $('#down').text(rat[1]);
    }, 'html');
}
$("#subscriptionAdd, #questionAdd, #questionEdit, #questionDelete, #answerEdit, #answerDelete, #userEdit, #userDelete,#subscriptionDelete").tooltip({
        delay:{
            show:10,
            hide:0
        }
});
var $sentAlert = $("#alertMessage");
$sentAlert.show();
$sentAlert.on("close.bs.alert",function(){
    $sentAlert.hide();
    return false;
});