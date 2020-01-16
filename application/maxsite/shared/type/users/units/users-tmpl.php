<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<h1>{{ $comusers_nik }}</h1>

<img src="{{ $avatar }}" alt="" class="mso-gravatar">

{% if ($no_activation_link) : %}
	<p><span style="color: red;">{{ tf('Активация не завершена.') }}</span> <a href="{{ $no_activation_link }}">{{ tf('Завершить') }}</a></p>
{% endif %}

{% if ($comusers_date_registr) : %}
	<p><b>{{ tf('Дата регистрации') }}:</b> {{ $comusers_date_registr }}</p>
{% endif %}

{% if ($comusers_nik) : %}
	<p><b>{{ tf('Ник') }}:</b> {{ $comusers_nik }}</p>
{% endif %}

{% if ($comusers_count_comments) : %} 
	<p><b>{{ tf('Комментариев') }}:</b> {{ $comusers_count_comments }}</p>
{% endif %}

{% if ($comusers_url) : %}
	<p><b>{{ tf('Сайт') }}:</b> <a rel="nofollow" href="{{ $comusers_url }}">{{ $comusers_url }}</a></p>
{% endif %}

{% if ($comusers_icq) : %}
	<p><b>{{ tf('ICQ') }}:</b> {{ $comusers_icq }}</p>
{% endif %}

{% if ($comusers_msn) : %} 
	<p><b>{{ tf('Twitter') }}:</b> <a rel="nofollow" href="https://twitter.com/{{ $comusers_msn }}">@{{ $comusers_msn }}</a></p>
{% endif %}

{% if ($comusers_jaber) : %}
	<p><b>{{ tf('Jabber') }}:</b> {{ $comusers_jaber }}</p>
{% endif %}

{% if ($comusers_skype) : %} 
	<p><b>{{ tf('Skype') }}:</b> {{ $comusers_skype }}</p>
{% endif %}
	
{% if ($comusers_date_birth) : %}
	<p><b>{{ tf('Дата рождения') }}:</b> {{ $comusers_date_birth }}</p>
{% endif %}

{% if ($comusers_description) : %} 
	<p><b>{{ tf('О себе') }}:</b> {{ $comusers_description }}</p>
{% endif %}

{% if ($comusers_admin_note) : %}
	<p><b>{{ tf('Примечание админа') }}:</b> {{ $comusers_admin_note }}</p>
{% endif %}

{% if ($edit_link) : %}
	<p><a href="{{ $edit_link }}">{{ tf('Редактировать данные') }}</a></p>
{% endif %}
