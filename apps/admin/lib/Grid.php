<?php

namespace admin;

/**
 * Stores an array for display as an inline-editable responsive grid.
 * Implements the `Iterator` interface so you can do:
 *
 *     $grid = new admin\Grid ($grid_structure);
 *     
 *     foreach ($grid as $r => $row) {
 *         foreach ($row->cols as $c => $col) {
 *             // etc.
 *         }
 *     }
 *
 * You can also manipulate the grid via:
 *
 *     $grid->add_row ('33,33,33', '', false, '', array ('Col A', 'Col B', 'Col C'));
 *     $grid->collapse (0, 1);
 *     echo $grid->current ()->units; // 50,50
 *     echo $grid->current ()->cols[1]; // Col B
 *     $grid->update (0, 1, 'New content for the second column of the first row.');
 *     $grid->property (0, 'bg_image', '/files/background.jpg');
 *     $grid->delete_row (0);
 *
 * Each row contains the following properties:
 *
 * - units         // Columns as a string e.g., 33,66
 * - css_class     // The class to assign this row
 * - equal_heights // Whether the height should be equal across columns
 * - bg_image      // Background image for the row
 * - cols          // The columns themselves
 * - fixed         // Whether this row should have a fixed background attachment
 * - inset         // Whether this row should be styled inset (e.g., shadows)
 * - height        // The height of the row
 *
 * Valid units values are:
 *
 * - 100
 * - 50,50
 * - 66,33
 * - 33,66
 * - 75,25
 * - 25,75
 * - 80,20
 * - 20,80
 * - 33,33,33
 * - 25,50,25
 * - 20,60,20
 * - 25,25,25,25
 * - 20,20,20,20,20
 *
 * Each column is simply an HTML string, which should be parsed for
 * embeds via:
 *
 *     echo $tpl->run_includes ($col);
 */
class Grid implements \Iterator {
	/**
	 * The grid structure.
	 */
	private $grid = array ();

	/**
	 * A list of valid CSS classes and their names,
	 * which can be assigned to rows.
	 */
	public $styles = array ();
	
	/**
	 * Position of iterator.
	 */
	private $position = 0;
	
	/**
	 * Valid units.
	 */
	private $valid_units = array (
		'100', '50,50', '66,33', '66,33', '75,25', '25,75', '80,20', '20,80',
		'33,33,33', '25,50,25', '20,60,20', '25,25,25,25', '20,20,20,20,20'
	);

	/**
	 * Starts with a JSON string or parsed array of the grid.
	 */
	public function __construct ($grid = array (), $styles = null) {
		$this->grid = is_array ($grid) ? $grid : json_decode ($grid);
		$this->styles = is_array ($styles) ? $styles : Layout::styles ();
		$this->position = 0;
	}
	
	/**
	 * Rewind the iterator to the first element.
	 */
	public function rewind () {
		$this->position = 0;
	}
	
	/**
	 * Return the current element.
	 */
	public function current () {
		return $this->grid[$this->position];
	}
	
	/**
	 * Return the key of the current element.
	 */
	public function key () {
		return $this->position;
	}

	/**
	 * Move forward to the next element.
	 */	
	public function next () {
		++$this->position;
	}
	
	/**
	 * Checks if current position is valid.
	 */
	public function valid () {
		return isset ($this->grid[$this->position]);
	}

	/**
	 * Collapse a row by deleting the specified column
	 * and adjusting the units accordingly.
	 *
	 * Columns should collapse in the following way:
	 *
	 * - 5 columns        ->     25,25,25,25
	 * - 4 columns        ->     33,33,33
	 * - 3 columns        ->     50,50
	 * - 2 columns        ->     100
	 *
	 * If a row has only one column, delete the row itself.
	 */
	public function collapse ($row, $col) {
		$convert_to = array (
			2 => '100',
			3 => '50,50',
			4 => '33,33,33',
			5 => '25,25,25,25'
		);
		
		$count = count ($this->grid[$row]->cols);
		if (! isset ($convert_to[$count])) {
			return $this->delete_row ($row);
		}
		$new_units = $convert_to[$count];
		$this->grid[$row]->units = $convert_to[$count];
		$col = array_splice ($this->grid[$row]->cols, $col, 1);
		return $col[0];
	}

	/**
	 * Add a row to the end of the grid.
	 */
	public function add_row ($units = '100', $css_class = '', $equal_heights = false, $bg_image = '', $fixed = false, $inset = false, $height = '', $cols = array ()) {
		$this->grid[] = (object) array (
			'units' => $units,
			'css_class' => $css_class,
			'equal_heights' => $equal_heights,
			'bg_image' => $bg_image,
			'cols' => $cols,
			'fixed' => $fixed,
			'inset' => $inset,
			'height' => $height
		);
	}

	/**
	 * Delete a row from the grid.
	 */
	public function delete_row ($row) {
		array_splice ($this->grid, $row, 1);
	}
	
	/**
	 * Return the whole grid, optionally JSON encoded as a string.
	 */
	public function all ($encode = false) {
		return $encode ? json_encode ($this->grid) : $this->grid;
	}
	
	/**
	 * Update the contents of a column. Returns false if the specified
	 * column doesn't exist, otherwise returns true.
	 */
	public function update ($row, $col, $content) {
		if (isset ($this->grid[$row]) && isset ($this->grid[$row]->cols[$col])) {
			$this->grid[$row]->cols[$col] = $content;
			return true;
		}
		return false;
	}
	
	/**
	 * Get the contents of a column. Returns false if the specified
	 * column doesn't exist.
	 */
	public function content ($row, $col) {
		if (isset ($this->grid[$row]) && isset ($this->grid[$row]->cols[$col])) {
			return $this->grid[$row]->cols[$col];
		}
		return false;
	}
	
	/**
	 * Gets or sets a property of a row. Returns null if getting or setting
	 * the value value of a nonexistant row, otherwise returns the latest
	 * value.
	 */
	public function property ($row, $property, $value = null) {
		if ($value === null) {
			return (isset ($this->grid[$row]) && isset ($this->grid[$row]->{$property}))
				? $this->grid[$row]->{$property}
				: null;
		}
		if (isset ($this->grid[$row]) && isset ($this->grid[$row]->{$property})) {
			$this->grid[$row]->{$property} = $value;
			return $value;
		}
		return null;
	}

	/**
	 * Is the specified units value valid?
	 */	
	public function valid_units ($units) {
		if (! in_array ($units, $this->valid_units)) {
			return false;
		}
		return true;
	}
}
