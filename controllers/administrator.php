<?php
class Administrator extends Controller{

    public function __construct(){
        parent::__construct();
        Session::init();
        $logged = Session::get('loggedIn');
        $status = Session::get('role');
        if($logged == false && $status != 'Administrator'){
            unset($logged);
            unset($status);
            Session::destroy();
            header('location: '.URL);
           die;
        }
    }

    public function index(){
        //Css files
        $this->view->css = array(
            'public/css/smartadmin-production.min.css',
            'node_modules/bootstrap/dist/css/bootstrap.min.css',
            'node_modules/font-awesome-4.4.0/css/font-awesome.min.css',
            'public/css/ng_animation.css',
            'public/css/open_sans.css',
            'node_modules/angular-datatables/dist/plugins/bootstrap/datatables.bootstrap.min.css',
            'public/css/jquery-ui.min.css',
            'public/css/dataTables.jqueryui.min.css',
            'public/css/awesome-bootstrap-checkbox.css',
            'public/css/style.css',
            //'public/css/printcss.css',
        );

        //angular Module
        $this->view->angular_modules = array(
            'node_modules/angular/angular.min.js',
            'node_modules/angular-route/angular-route.min.js',
            'node_modules/angular-sanitize/angular-sanitize.min.js',
            'node_modules/angular-animate/angular-animate.min.js',
            'node_modules/angular-resource/angular-resource.min.js',
            'node_modules/angular-messages/angular-messages.min.js',
            'node_modules/angular-cookies/angular-cookies.min.js',
        );

        //dataTables// dataTables.lightColumnFilter.min
        $this->view->datatables_modules = array(

            'node_modules/datatables.net/js/jquery.dataTables.js',
            'node_modules/angular-datatables/vendor/datatables-tabletools/js/dataTables.tableTools.js',
            'node_modules/angular-datatables/dist/angular-datatables.min.js',
            'node_modules/angular-datatables/dist/plugins/bootstrap/angular-datatables.bootstrap.min.js',
            'node_modules/angular-datatables/dist/plugins/colreorder/angular-datatables.colreorder.min.js',
            'node_modules/angular-datatables/dist/plugins/dataTablescolumnFilter/jquery.dataTables.columnFilter.js',
            'node_modules/angular-datatables/dist/plugins/columnfilter/angular-datatables.columnfilter.js',
            'node_modules/angular-datatables/dist/plugins/colvis/angular-datatables.colvis.min.js',
            'node_modules/angular-datatables/dist/plugins/fixedcolumns/angular-datatables.fixedcolumns.min.js',
            'node_modules/angular-datatables/dist/plugins/fixedheader/angular-datatables.fixedheader.min.js',
            'node_modules/angular-datatables/dist/plugins/scroller/angular-datatables.scroller.min.js',
            'node_modules/angular-datatables/dist/plugins/tabletools/angular-datatables.tabletools.min.js',
            'node_modules/angular-datatables/dist/plugins/dataTablescolumnFilter/dataTables.lightColumnFilter.min.js',
            'node_modules/angular-datatables/dist/plugins/light_columnfilter/dataTables.lightColumnFilter.js',
            'node_modules/angular-datatables/dist/plugins/buttons/dataTables.buttons.js',
            'node_modules/angular-datatables/dist/plugins/buttons/angular-datatables.buttons.js',
            'node_modules/angular-datatables/dist/plugins/responsive/dataTables.responsive.js',



        );

        //App Module
        $this->view->app_module = array(
            'app/app.js',
        );

        //Controller Module
        $this->view->controller_module = array(
            'app/controllers/mainController.js',
            'app/controllers/dashboardController.js',
            'app/controllers/wearehouseController.js',
            'app/controllers/userController.js',
            'app/controllers/clientController.js',
            'app/controllers/editUserController.js',
            'app/controllers/editClientController.js',
            'app/controllers/goodsController.js',
            'app/controllers/editGoodsController.js',
            'app/controllers/setupController.js',
            'app/controllers/pregledPrijemaMerkantila.js',
            'app/controllers/pregledOtpremaMerkantila.js',
            'app/controllers/pregledPrijemaRepromaterijal.js',
            'app/controllers/pregledOtpremeRepromaterijal.js',
            'app/controllers/rezervacijeController.js',
            'app/controllers/stanjeMerkantilaController.js',
            'app/controllers/stanjeRepromaterijalController.js',
            'app/controllers/dispozicijeController.js',
            'app/controllers/kupacRepromaterijalController.js',
        );

        //filters Module
        $this->view->component_module = array(
            'app/components/autocompleteClients.js'
        );

        //Directives Module
        $this->view->directives_module = array(
            'app/directives/winresize.js',
            'app/directives/isnumber.js',
            'app/directives/form_directive.js',
            'app/directives/smart_form_directive.js',
            'app/directives/fileModel.js',
        );

        //Services/Factories Module
        $this->view->service_module = array(
            'app/services/mine_service.js',
            'app/services/mineFactory.js',
            'app/services/google_map_service.js',
            'app/services/warehousesFactory.js',
            'app/services/placesFactory.js',
            'app/services/usersFactory.js',
            'app/services/error_service.js',
            'app/services/clientsFactory.js',
            'app/services/infoBox_service.js',
            'app/services/goodsFactory.js',
            'app/services/setupFactory.js',
            'app/services/fileUploadService.js',
            'app/services/prijemMerkantilaPregledFactory.js',
            'app/services/otpremaMerkantilaPregledFactory.js',
            'app/services/prijemRepromaterijalPregledFactory.js',
            'app/services/otpremaRepromaterijalPregledFactory.js',
            'app/services/rezervacijeFactory.js',
            'app/services/stanjeRepromaterijalFactory.js',
            'app/services/dispozicijeFactory.js',
            'app/services/dashboardFactory.js',
            'app/services/kupacRepromaterijalFactory.js',
            'app/services/mineFactory.js',
        );

        //filters Module
        $this->view->filters_module = array(
            'app/custom_filters/custom_filters.js',
            'app/custom_filters/validEmailFilter.js'
        );

        $this->view->render('administrator/index', true);

    }


}
?>
