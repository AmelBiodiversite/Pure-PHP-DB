<?php
/**
 * MARKETFLOW PRO - HOME CONTROLLER
 * Fichier : app/controllers/HomeController.php
 */


namespace App\Controllers;
use Core\Controller;

class HomeController extends Controller {
    public function index() {
        $this->view('home/index', ['title'=>'Accueil - MarketFlow Pro']);
    }
}
