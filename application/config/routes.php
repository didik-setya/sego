<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'dashboard';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;



$route['actionlogin']['POST'] = 'login/validation_login';
$route['check_access'] = 'login/check_access';
$route['to_login'] = 'login/access_to_kost';


$route['kamar'] = 'dashboard/data_kamar';
$route['validation_kamar']['POST'] = 'ajax/validation_kamar';
$route['delete_kamar']['POST'] = 'ajax/delete_kamar';



$route['penghuni'] = 'dashboard/data_penghuni';
$route['action_data_penghuni']['POST'] = 'ajax/action_penghuni';



$route['payment'] = 'dashboard/payment';
// $route['validation_payment']['POST'] = 'ajax/valid_payment';
$route['delete_payment']['POST'] = 'ajax/delete_payment';



$route['pengeluaran'] = 'dashboard/pengeluaran';
$route['validation_pengeluaran']['POST'] = 'ajax/valid_pengeluaran';
$route['delete_pengeluaran']['POST'] = 'ajax/delete_pengeluaran';



$route['setoran'] = 'dashboard/setoran';
$route['validation_setoran']['POST'] = 'ajax/valid_setor';
$route['delete_setor']['POST'] = 'ajax/delete_setor';



$route['report'] = 'dashboard/report';
$route['get_rekap_report']['POST'] = 'ajax/get_data_report';



$route['kost'] = 'dashboard/data_kost';
$route['action_kost']['POST'] = 'ajax/act_kost';
$route['delete_kost']['POST'] = 'ajax/delete_kost';
$route['get_kost']['POST'] = 'ajax/get_data_kost';



$route['access'] = 'dashboard/access_kost';
$route['action_user']['POST'] = 'ajax/action_user';
$route['access_kost'] = 'ajax/access_kost';



$route['settings'] = 'dashboard/settings';
$route['validate_settings']['POST'] = 'ajax/validation_settings';
$route['valid_password']['POST'] = 'ajax/validation_password';

$route['data_dashboard']['POST'] = 'ajax/data_dashboard';


$route['transaction'] = 'dashboard/transaction';

$route['load_data_transaksi'] = 'ajax/data_transaksi';
$route['get_data_pengeluaran'] = 'ajax/data_pengeluaran';
$route['get_data_setoran'] = 'ajax/data_setoran';
