<?php

$title = 'Update :: Interviews';

include __DIR__ . '/../common/session.php';
include __DIR__ . '/../common/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'update') {
    $interview = new Interview();

    if ($interview->update($_GET['id'], $_POST)) {
        $_SESSION['flash'] = '<div class="alert alert-info" role="alert">Interview updated successfully</div>';
    } else {
        $_SESSION['flash'] = '<div class="alert alert-danger" role="alert">Failed to update interview</div>';
    }
}

$interview = new Interview();
$interview = $interview->read($_GET['id']);

?>
<div class="header">
    <div class="row">
        <div class="col-6">
            <h1><i class="fas fa-address-book"></i> Interviews :: Update :: <?php echo $interview['first_name'] . ' ' . $interview['last_name']; ?></h1>
        </div>
        <div class="col-6">
            <div class="float-right"></div>
        </div>
    </div>
</div>
<?php if (!empty($_SESSION['flash'])) echo $_SESSION['flash']; unset($_SESSION['flash']); ?>
<?php if (isset($interview)) { ?>
<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-body">
                <form action="" id="frmUpdate" method="post">
                    <input name="action" value="update" type="hidden">

                    <h5>General Information</h5>
                    <hr />
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input name="first_name" type="text" class="form-control" id="first_name" aria-describedby="firstnameHelp" placeholder="First Name" value="<?php echo $interview['first_name']; ?>">
                                <small id="firstnameHelp" class="form-text text-muted">Enter the interviewees first name.</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input name="last_name" type="text" class="form-control" id="last_name" aria-describedby="lastnameHelp" placeholder="Last Name" value="<?php echo $interview['last_name']; ?>">
                                <small id="lastnameHelp" class="form-text text-muted">Enter the interviewees last name.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="email">E-Mail Address</label>
                                <input name="email" type="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="username@domain.com" value="<?php echo $interview['email']; ?>">
                                <small id="emailHelp" class="form-text text-muted">Enter the interviewees e-mail address.</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input name="phone" type="text" class="form-control" id="phone" aria-describedby="phoneHelp" placeholder="(000) 000-0000" value="<?php echo $interview['phone']; ?>">
                                <small id="phoneHelp" class="form-text text-muted">Enter the interviewees phone number in the proper format. <span class="text-info text-small">Formatting happens automatically.</span></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="date">Interview Date</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                                    </div>
                                    <input name="date" type="text" class="form-control" id="date" aria-describedby="dateHelp" placeholder="yyyy-mm-dd" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-autoclose="true" value="<?php echo $interview['date']; ?>">
                                </div>
                                <small id="dateHelp" class="form-text text-muted">Enter the interview date in the proper format. <span class="text-info text-small">Formatting happens automatically.</span></small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="method">Interview Method</label>
                                <input name="method" type="text" class="form-control" id="method" aria-describedby="methodHelp" placeholder="Phone, In Person, E-Mail, etc." value="<?php echo $interview['method']; ?>">
                                <small id="methodHelp" class="form-text text-muted">Enter the method user to perform the interview.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="qa">Questions asked by the Interviewee</label>
                                <textarea rows="5" name="qa" type="text" class="form-control" id="qa" aria-describedby="qaHelp" placeholder="Enter questions and answers that the interviewee asked..."><?php echo $interview['qa']; ?></textarea>
                                <small id="qaHelp" class="form-text text-muted">Enter any questions that the interviewee asked and the responses given.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="notes">Additional Notes</label>
                                <textarea rows="5" name="notes" type="text" class="form-control" id="notes" aria-describedby="notesHelp" placeholder="Enter any additional notes about the interviewee or the interview in general."><?php echo $interview['notes']; ?></textarea>
                                <small id="notesHelp" class="form-text text-muted">Enter any additional notes about the interviewee or the interview in general.</small>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="row">
                        <div class="col-2"><h5>Questions</h5></div>
                        <div class="col-3"><select class="form-control selectpicker" id="categories"></select></div>
                        <div class="col-5"><select class="form-control selectpicker" id="questions"></select></div>
                        <div class="col-2"><button type="button" class="btn btn-info btn-block btn-add-question"><i class="fas fa-plus"></i> Add Question</button></div>
                    </div>
                    <hr />
                    <div id="interview_questions">
                    <?php

                    $answers = new InterviewAnswer();
                    $answers = $answers->get($interview['id']);

                    if (count($answers) >= 1) {
                        foreach ($answers AS $answer) {
                            echo '<div class="form-group"><label for="question' . $answer['question_id'] . '">' . $answer['question'] . '</label><textarea rows="5" name="answer[' . $answer['question_id'] . ']" class="form-control" id="question' . $answer['question_id'] . '">' . $answer['answer'] . '</textarea></div>';
                        }
                    } else {
                        echo '<div class="alert alert-danger" role="alert">No interview questions</div>';
                    }

                    ?>
                    </div>
                    <div class="form-group">
                        <label for="hire">Should we hire this person?</label>
                        <select class="form-control selectpicker" name="hire">
                            <option value="0"<?php echo ($interview['hire'] == 0 ? ' selected' : ''); ?>>No</option>
                            <option value="1"<?php echo ($interview['hire'] == 1 ? ' selected' : ''); ?>>Yes</option>
                            <option value="2"<?php echo ($interview['hire'] == 2 ? ' selected' : ''); ?>>Unsure</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6"><a class="btn btn-block btn-outline-dark" href="/interviews/"><i class="fas fa-ban"></i> Cancel</a></div>
                        <div class="col-6"><button type="submit" class="btn btn-block btn-info"><i class="fas fa-save"></i> Save Interview</button></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#date').mask('0000-00-00', {placeholder: "yyyy-mm-dd"});
        $('#phone').mask('(000) 000-0000', {placeholder: "(000) 000-0000"});

        $.ajax({
            url: '/api/questions_categories.php',
            method: 'GET',
            cache: false,
            dataType: 'json',
            success: function(data) {
                var categories = '<option value="0">-- Select Question Category --</option>';

                $.each(data, function(index, category) {
                    categories += '<option value="' + category['id'] + '">' + category['name'] + '</option>';
                });

                $('#categories').append(categories).selectpicker('refresh');
            }
        });

        $('#categories').change(function() {
            $.ajax({
                url: '/api/questions.php',
                method: 'GET',
                cache: false,
                dataType: 'json',
                data: {
                    questions_categories_id: $('#categories').val()
                },
                success: function(data) {
                    var questions = '<option value="0">-- Select Question --</option>';

                    $.each(data, function(index, question) {
                        questions += '<option value="' + question['id'] + '">' + question['name'] + '</option>';
                    });

                    $('#questions').find('option').remove().end().append(questions).selectpicker('refresh');
                }
            });
        });

        $('.btn-add-question').click(function(e) {
            e.preventDefault();

            $.ajax({
                url: '/api/questions.php',
                method: 'GET',
                cache: false,
                dataType: 'json',
                data: {
                    id: $('#questions').val()
                },
                success: function(data) {
                    $('#interview_questions').append('<div class="form-group"><label for="question' + data['id'] + '">' + data['question'] + '</label><textarea rows="5" name="answer[' + data['id'] + ']" class="form-control" id="question' + data['id'] + '"></textarea></div>');
                }
            });
        });

        $("#frmUpdate").validate({
            rules: {
                first_name: {
                    required: true,
                    normalizer: function(value) {
                        return $.trim(value);
                    },
                    minlength: 2,
                    maxlength: 32
                },
                last_name: {
                    required: true,
                    normalizer: function(value) {
                        return $.trim(value);
                    },
                    minlength: 2,
                    maxlength: 32
                },
                email: {
                    required: true,
                    normalizer: function(value) {
                        return $.trim(value);
                    },
                    email: true
                },
                phone: {
                    required: true
                },
                date: {
                    required: true,
                    normalizer: function(value) {
                        return $.trim(value);
                    },
                    dateISO: true
                },
                method: {
                    required: true,
                    normalizer: function(value) {
                        return $.trim(value);
                    },
                    minlength: 2,
                    maxlength: 64
                }
            }
        });
    });
</script>
<?php } ?>
<?php include __DIR__ . '/../common/footer.php'; ?>
