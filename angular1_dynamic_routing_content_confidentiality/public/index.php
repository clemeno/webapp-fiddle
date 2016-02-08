<?php

define('__ROOT__', dirname(dirname(__FILE__)));

/**
 * Jean Toto, the sample user
 */
class JeanToto
{
    public static $SAMPLE_USER = [
        'id'                           => 'a34bd479-ec2c-4954-8463-499102327ff1',
        'login'                        => 'toto',
        'plain_password'               => 'jeantoto',
        'first_name'                   => 'Jean',
        'last_name'                    => 'Toto',
        'email'                        => 'toto.jean@email.com',
        'fine_grained_custom_accesses' => ['ROLE_MANAGE_ANNOUNCEMENT', 'ROLE_MANAGE_CHAT'],
    ];

    public static function hasRole( $input_role ) {
        return ( $input_role && in_array('' . $input_role, JeanToto::$SAMPLE_USER['fine_grained_custom_accesses']) );
    }
}

?>

<!DOCTYPE html>
<html lang="en" ng-app="dynamicConfidentialityAngular">
<head>
    <meta charset='utf-8'>
    <meta content='width=device-width' name='viewport'>
    <meta content='CLEm - clemeno @ github' name='description'>
    <title>Dynamic Routing + Content Confidentiality Angular</title>
    <!-- css imports -->
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/angular_material/1.0.5/angular-material.min.css">
    <!-- css custom -->
    <!-- javascript imports for the engine in header -->
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-route.js"></script>
</head>
<body>
    <!-- page content -->
    <div ng-view></div>
    <!-- javascript imports for the view can come later in the body -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/angular.js/1.5.0/angular-aria.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/angular.js/1.5.0/angular-animate.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angular_material/1.0.5/angular-material.min.js"></script>
    <!-- custom javascript app dependencies common -->
    <script><?php echo file_get_contents(__ROOT__ . '/private/modules/home/homeController.js'); ?></script>
    <!-- custom javascript app dependencies dynamic -->
    <?php if( JeanToto::hasRole('ROLE_MANAGE_ANNOUNCEMENT') ): ?>
    <script><?php echo file_get_contents(__ROOT__ . '/private/shared/announcementBoard/announcementBoardController.js'); ?></script>
    <?php endif; ?>
    <?php if( JeanToto::hasRole('ROLE_MANAGE_CHAT') || JeanToto::hasRole('ROLE_DISPLAY_CHAT') ): ?>
    <script><?php echo file_get_contents(__ROOT__ . '/private/shared/chat/chatController.js'); ?></script>
    <?php endif; ?>
    <?php if( JeanToto::hasRole('ROLE_MANAGE_CHAT') ): ?>
    <script><?php echo file_get_contents(__ROOT__ . '/private/shared/chat/chatManager/chatManagerController.js'); ?></script>
    <?php endif; ?>
    <!-- custom javascript app -->
    <script>
        var dynamic = {
            list: {
                dependencies: ( function() {
                    // init with commonly disclosed angular dependencies
                    var modules = [ 'ngRoute', 'ngMaterial', 'homeController' ];
                    // add dynamicaly role dependent dependencies and module contents
                    // disclosed only to certain users (e.g. w/ roles)
                    <?php if( in_array('ROLE_MANAGE_ANNOUNCEMENT', JeanToto::$SAMPLE_USER['fine_grained_custom_accesses']) ): ?>
                    modules.push( 'announcementBoardController' );
                    <?php endif; ?>
                    <?php if( JeanToto::hasRole('ROLE_MANAGE_CHAT') || JeanToto::hasRole('ROLE_DISPLAY_CHAT') ): ?>
                    modules.push( 'chatController' );
                    <?php endif; ?>
                    <?php if( JeanToto::hasRole('ROLE_MANAGE_CHAT') ): ?>
                    modules.push( 'chatManagerController' );
                    <?php endif; ?>
                    return modules;
                } )(),
                url_template_controler: [] // init with commonly disclosed module contents
            },
            ngApp: null
        };
        dynamic.ngApp = angular.module( 'dynamicConfidentialityAngular', dynamic.list.dependencies );
    </script>
</body>
</html>
