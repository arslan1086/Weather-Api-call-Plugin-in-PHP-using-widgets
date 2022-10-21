<?php

/**
 * Plugin Name: simple-weather-widget
 * Description: Simple weather widget.
 * Version: 1.0
 */

/**
 * Add function to widgets_init that'll load our widget.
 */

add_action('widgets_init', 'simple_weather_load_widgets');

function simple_weather_load_widgets()
{
	register_widget('simple_weather_widget');
}


class simple_weather_widget extends WP_Widget
{
	/**
	 * Widget setup.
	 */
	function simple_weather_widget()
	{
		/* Widget settings. */
		$widget_options = array(
			'classname' => 'simple_weather_widget',
			'description' => __('A Simple widget that displays weather.')
		);

		/* Widget control settings. */
		$control_options = array(
			'width' => 300,
			'height' => 350,
			'id_base' => 'simple_weather_widget'
		);

		/* Create the widget. */
		$this->WP_Widget('simple_weather_widget', 'Simple Weather Widget', $widget_options, $control_options);
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget($args, $instance)
	{
		extract($args);

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title']);
		$woeid = ($instance['woeid'] != "") ? $instance['woeid'] : 12799205;


		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ($title)
			echo $before_title . $title . $after_title;
		echo " \n";
		// $url = "http://api.openweathermap.org/data/2.5/weather?q=$woeid&appid=49c0bad2c7458f1c76bec9654081a661";
		$temperature = round($result['main']['temp'] - 273.15);
		$weather = $result['weather'][0]['main'];
		$date = date('d M', $result['dt']);
		$logo = $result['weather'][0]['icon'];
		var_dump($logo);
		$designWeather = "
	<article class='widget' style='position: absolute; margin-top:50px; top: 50%; left: 50%; display: flex; height: 300px; width: 600px; transform: translate(-50%, -50%); flex-wrap: wrap; cursor: pointer; border-radius: 20px; box-shadow: 0 27px 55px 0 rgba(0, 0, 0, 0.3), 0 17px 17px 0 rgba(0, 0, 0, 0.15);'>
	<div class='weatherIcon' style='flex: 1 100%; height: 60%; border-top-left-radius: 20px; border-top-right-radius: 20px; background: #FAFAFA; font-family: weathericons; display: flex; align-items: center; justify-content: space-around; font-size: 100px;'>
		<img src='http://openweathermap.org/img/wn/{$logo}@4x.png' />
	</div>
	<div class='weatherInfo' style='flex: 0 0 70%; height: 40%; background: darkslategray; border-bottom-left-radius: 20px; display: flex; align-items: center; color: white;'>
		<div class='temperature' style='flex: 0 0 40%; width: 100%; font-size: 65px; display: flex; justify-content: space-around;'>
			<span>{$temperature}°</span>
		</div>
		<div class='description mr45' style='flex: 0 60%; display: flex; flex-direction: column; width: 300px; height: 600px; justify-content: center; margin-left:-15px;'>
			<div class='weatherCondition' style='text-transform: uppercase; font-size: 35px; font-weight: 100;'>{ $weather}</div><br>
			<div class='place' style='font-size: 15px;'>ID: {$result['id']}</div>
			<div class='place' style='font-size: 15px;'>City: {$result['name']}</div>
			<div class='place' style='font-size: 15px;'>Timezone:{$result['timezone']}</div>
			<div class='place' style='font-size: 15px;'>base: {$result['base']}</div>
		</div>

		<div class='description'>
			<div class='weatherCondition' style='text-transform: uppercase; font-size: 35px; font-weight: 100;'>Wind</div><br>
			<div class='place' style='font-size: 15px;'> {$result['wind']['speed']} M/H</div>
			<div class='place' style='font-size: 15px;'>Longitude:  {$result['coord']['lon']}</div>
		</div>
	</div>
	<div class='date' style='flex: 0 0 30%; height: 40%; background: #70C1B3; border-bottom-right-radius: 20px; display: flex; justify-content: space-around; align-items: center; color: white; font-size: 30px; font-weight: 800;'>
		{$date}
	</div>
</article>
		";




		$url = "http://api.openweathermap.org/data/2.5/weather?q=$woeid&appid=49c0bad2c7458f1c76bec9654081a661";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);

		curl_close($ch);
		$result = json_decode($result, true);

		if ($result['cod'] == 200) {
			echo $designWeather;
		} else {
			$msg = $result['message'];
		}
		/* After widget (defined by themes). */
		echo $after_widget;
	}



	/**
	 * Update the widget settings.
	 */
	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['woeid'] = strip_tags($new_instance['woeid']);

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form($instance)
	{

		/* Set up some default widget settings. */
		$defaults = array(
			'title' => __('weather', 'weather'),
			'woeid' => __('2490383', '2490383')
		);
		$instance = wp_parse_args((array) $instance, $defaults); ?>

		<!-- Widget Title: Title Input -->
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'title'); ?></label>
			<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<!-- Woeid : woeid Input -->

		<p>
			<label for="<?php echo $this->get_field_id('woeid'); ?>"><?php _e('woeid:', 'woeid'); ?></label>
			<input id="<?php echo $this->get_field_id('woeid'); ?>" name="<?php echo $this->get_field_name('woeid'); ?>" value="<?php echo $instance['woeid']; ?>" style="width:100%;" />
		</p>



<?php
	}
}
?>