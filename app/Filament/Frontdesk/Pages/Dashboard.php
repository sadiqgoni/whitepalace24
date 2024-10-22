<?php
 
namespace App\Filament\Frontdesk\Pages;
use App\Filament\Frontdesk\Widgets\LatestCheckedIn;
use App\Filament\Frontdesk\Widgets\LatestCheckedOut;
use App\Filament\Frontdesk\Widgets\LatestConfirmedReservation;
use App\Filament\Frontdesk\Widgets\StatsOverview;
 
class Dashboard extends \Filament\Pages\Dashboard
{
    protected static string $routePath = 'dashboard';  
      protected ?string $maxContentWidth = 'full';

      
        protected static ?string $title = 'Dashboard';
    
        protected static ?string $navigationLabel = 'Dashboard';
    
        protected ?string $heading = 'Dashboard';
        
        protected ?string $subheading = '';
        public $showLatestChecked = true; // Show "Checked-In" table by default
        public $showLatestCheckedOut = false; // Hide "Checked-Out" table by default
        public $showLatestConfirmedReservation = false; 
        protected $listeners = [
            'showCheckedInGuestsTable' => 'showCheckedIn',
            'showCheckedOutGuestsTable' => 'showCheckedOut',
            'showConfirmedReservationTable' => 'showConfirmedReservation',
    
        ];
    
        // Method to show "Checked-In" table and hide the "Checked-Out" table
        public function showCheckedIn()
        {
            $this->showLatestChecked = true;
            $this->showLatestCheckedOut = false; 
            $this->showLatestConfirmedReservation = false; 
    
        }
    
        // Method to show "Checked-Out" table and hide the "Checked-In" table
        public function showCheckedOut()
        {
            $this->showLatestCheckedOut = true;
            $this->showLatestChecked = false;
            $this->showLatestConfirmedReservation = false; 
    
        }
        public function showConfirmedReservation()
        {
            $this->showLatestConfirmedReservation = true; 
            $this->showLatestChecked = false;
            $this->showLatestCheckedOut = false;
        }
    
    
        public function getWidgets(): array
        {
            $widgets = [
                StatsOverview::class,  
            ];
    
            if ($this->showLatestChecked) {
                $widgets[] = LatestCheckedIn::class;
            }
    
            if ($this->showLatestCheckedOut) {
                $widgets[] = LatestCheckedOut::class;
            }
            if ($this->showLatestConfirmedReservation) {
                $widgets[] = LatestConfirmedReservation::class;
            }
    
            return $widgets;
        }
    }