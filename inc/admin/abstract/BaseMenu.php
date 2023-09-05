<?php

abstract class BaseMenu {

	protected string $pageTitle;
	protected string $menuTitle;
	protected string $capability;
	protected string $menuSlug;
	protected string $menuIcon;
	protected bool $hasSubMenu = false;
	protected array $subMenuItems;

	public function __construct() {
		$this->capability = 'manage_options';
		add_action( 'admin_menu', [ $this, 'register_menu' ] );
	}

	// Common method
	public function register_menu(): void {

		add_menu_page( $this->pageTitle,
			$this->menuTitle,
			$this->capability,
			$this->menuSlug,
			[ $this, 'page' ],
			$this->menuIcon );

		if ( $this->hasSubMenu ) {
			foreach ( $this->subMenuItems as $subMenuItem ) {
				// Adds a submenu page.
				$hook = add_submenu_page( $this->menuSlug,
					$subMenuItem['pageTitle'],
					$subMenuItem['menuTitle'],
					$this->capability,
					$subMenuItem['menuSlug'],
					[ $this, $subMenuItem['callback'] ],
				);

				// Fires before a particular screen is loaded.
				if ($subMenuItem['load']['status']){
					add_action('load-' . $hook , [$this, $subMenuItem['load']['callback']]);
				}
			}
		}

		// Remove a submenu page.
		remove_submenu_page($this->menuSlug,$this->menuSlug);
	}

	// Force Extending class to define this method
	abstract public function page();


}