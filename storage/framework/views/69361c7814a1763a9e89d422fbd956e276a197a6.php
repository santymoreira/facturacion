<?php $__env->startSection('head'); ?>

<link href="<?php echo e(asset('css/bootstrap.min.css')); ?>" rel="stylesheet" type="text/css"/>
<link href="<?php echo e(asset('css/built.public.min.css')); ?>" rel="stylesheet" type="text/css"/>

<style type="text/css">
    body {
        padding-top: 40px;
        padding-bottom: 40px;
    }
    .modal-header {
        border-top-left-radius: 3px;
        border-top-right-radius: 3px;
        background-color: #337ab7;
    }
    .modal-header h4 {
        margin:0;
        color:#fff;
    }
    .modal-header img {
        float: left;
        margin-right: 20px;
    }
    .form-signin {
        max-width: 400px;
        margin: 0 auto;
        background: #fff;
    }
    p.link a {
        font-size: 11px;
    }
    .form-signin .inner {
        padding: 20px;
        border-bottom-right-radius: 3px;
        border-bottom-left-radius: 3px;
        border-left: 1px solid #ddd;
        border-right: 1px solid #ddd;
        border-bottom: 1px solid #ddd;
    }
    .form-signin .checkbox {
        font-weight: normal;
    }
    .form-signin .form-control {
        margin-bottom: 17px !important;
    }
    .form-signin .form-control:focus {
        z-index: 2;
    }

    .modal-header a:link,
    .modal-header a:visited,
    .modal-header a:hover,
    .modal-header a:active {
        text-decoration: none;
        color: white;
    }

    .form-control {
        display: block;
        width: 100%;
        height: 40px;
        padding: 9px 12px;
        font-size: 16px;
        line-height: 1.42857143;
        color: #000 !important;
        background: #f9f9f9 !important;
        background-image: none;
        border: 1px solid #dfe0e1;
        border-radius: 2px;
        -webkit-box-shadow: none;
        box-shadow: none;
        -webkit-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
        transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
    }

</style>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('body'); ?>
<div class="container">

    <?php echo $__env->make('partials.warn_session', ['redirectTo' => '/login'], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <?php echo Former::open('login')
            ->rules(['email' => 'required|email', 'password' => 'required'])
            ->addClass('form-signin'); ?>

    <?php echo e(Former::populateField('remember', 'true')); ?>


    <div class="modal-header">
        <?php if(Utils::isWhiteLabel()): ?>
            <h4><?php echo e(trans('texts.account_login')); ?></h4>
        <?php else: ?>
            <a href="<?php echo e(NINJA_WEB_URL); ?>" target="_blank">
                <img src="<?php echo e(asset('images/icon-login.png')); ?>" />
                <h4>Invoice Ninja | <?php echo e(trans('texts.account_login')); ?></h4>
            </a>
        <?php endif; ?>
    </div>
        <div class="inner">
            <p>
                <?php echo Former::text('email')->placeholder(trans('texts.email_address'))->raw(); ?>

                <?php echo Former::password('password')->placeholder(trans('texts.password'))->raw(); ?>

                <?php echo Former::hidden('remember')->raw(); ?>

            </p>

            <p><?php echo Button::success(trans('texts.login'))
                    ->withAttributes(['id' => 'loginButton'])
                    ->large()->submit()->block(); ?></p>

            <?php if(Input::get('new_company') && Utils::allowNewAccounts()): ?>
                <?php echo Former::hidden('link_accounts')->value('true'); ?>

                <center><p>- <?php echo e(trans('texts.or')); ?> -</p></center>
                <p><?php echo Button::primary(trans('texts.new_company'))->asLinkTo(URL::to('/invoice_now?new_company=true&sign_up=true'))->large()->submit()->block(); ?></p><br/>
            <?php elseif(Utils::isOAuthEnabled()): ?>
                <center><p>- <?php echo e(trans('texts.or')); ?> -</p></center>
                <div class="row">
                <?php foreach(App\Services\AuthService::$providers as $provider): ?>
                    <div class="col-md-6">
                        <a href="<?php echo e(URL::to('auth/' . $provider)); ?>" class="btn btn-primary btn-block social-login-button" id="<?php echo e(strtolower($provider)); ?>LoginButton">
                            <i class="fa fa-<?php echo e(strtolower($provider)); ?>"></i> &nbsp;
                            <?php echo e($provider); ?>

                        </a><br/>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <p class="link">
                <?php echo link_to('/recover_password', trans('texts.recover_password'), ['class' => 'pull-left']); ?>

                <?php echo link_to(NINJA_WEB_URL.'/knowledgebase/', trans('texts.knowledge_base'), ['target' => '_blank', 'class' => 'pull-right']); ?>

            </p>
            <br/>

            <?php if(count($errors->all())): ?>
                <div class="alert alert-danger">
                    <?php foreach($errors->all() as $error): ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if(Session::has('warning')): ?>
            <div class="alert alert-warning"><?php echo Session::get('warning'); ?></div>
            <?php endif; ?>

            <?php if(Session::has('message')): ?>
            <div class="alert alert-info"><?php echo Session::get('message'); ?></div>
            <?php endif; ?>

            <?php if(Session::has('error')): ?>
            <div class="alert alert-danger"><li><?php echo Session::get('error'); ?></li></div>
            <?php endif; ?>

        </div>

        <?php echo Former::close(); ?>


        <p/>

    </div>


    <script type="text/javascript">
        $(function() {
            if ($('#email').val()) {
                $('#password').focus();
            } else {
                $('#email').focus();
            }
        })
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>