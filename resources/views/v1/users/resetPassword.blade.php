@extends(__v() . '.layouts.app')

@section('content')
<section class="content">
    <div class="row">
        <div class="col-md-12 can-focus">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Reset Password</h3>
                    <div class="box-tools pull-right">
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <form id="ResetPassForm"
                                action="/users/{{$data['id']}}/update-password"
                                class="form-horizontal fv-form fv-form-bootstrap" method="POST">
                                @csrf
                                @method('POST')
                                <div class="form-group">
                                    <label for="password"class="col-sm-2 control-label">New Password <span class="star" style="color:red">*</span></label>
                                    <div class="col-sm-6">
                                        <input type="password" class="form-control" id="password"
                                            placeholder="Password"
                                            name="password" autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="confirm_password" class="col-sm-2 control-label">Confirm Password<span class="star" style="color:red">*</span></label>
                                    <div class="col-sm-6">
                                        <input type="password" class="form-control" id="confirm_password"
                                            placeholder="Confirm Password"
                                            name="confirm_password" autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-9 col-sm-offset-2">
                                        <a href="{{url('users')}}" class="btn btn-md btn-flat btn-danger">{{ __('global.buttons.cancel') }}</a>
                                        <button type="submit" class="btn btn-md btn-flat btn-success">{{ __('global.buttons.save') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')

<Script>
    const ResetPassForm = $('#ResetPassForm');

    const validators = {
        
        password: {
            validators: {
                notEmpty: {}
            }
        },
        confirm_password: {
            validators: {
                identical: {
                    field: 'password',
                    message: 'Password confirmation does not match'
                },
                notEmpty: {}
            }
        },
        
    };
    

    ResetPassForm.callFormValidation(validators).on('success.form.fv', function (e) {
        e.preventDefault();
        $('.content').find('.box').waitMeShow();
        const $form = $(e.target),
        fv = $form.data('formValidation');
        const Axios = axios.post($form.attr('action'), $form.serialize());
        Axios.then((response) => {
            successResponse(response.data);
            setTimeout(() => {
                window.location.href = '/';
            }, 1000);
        });
        Axios.catch((error) => {
            failedResponse(error);
        });
    })
</Script>


@endsection
