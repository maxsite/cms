<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function popup_autoload()
{
	$options = mso_get_option('plugin_popup', 'plugins', array());
	
	if (isset($options['popup-content']) and $options['popup-content'])
	{
		mso_hook_add('body_end', 'popup_body_end');
		mso_hook_add('head', 'popup_head');
	}
}

# функция выполняется при активации (вкл) плагина
function popup_activate($args = array())
{	
	mso_create_allow('popup_edit', t('Админ-доступ к настройкам') . ' ' . t('PopUp'));
	return $args;
}


# функция выполняется при деинсталяции плагина
function popup_uninstall($args = array())
{	
	mso_delete_option('plugin_popup', 'plugins' ); // удалим созданные опции
	mso_remove_allow('popup_edit'); // удалим созданные разрешения
	return $args;
}

function popup_head($args = array())
{
	$options = mso_get_option('plugin_popup', 'plugins', array());
	if (!isset($options['popup-bottom'])) $options['popup-bottom'] = 500;
	if (!isset($options['popup-cookie'])) $options['popup-cookie'] = 30;
	if (!isset($options['popup-fade'])) $options['popup-fade'] = 600;
	if (!isset($options['popup-position'])) $options['popup-position'] = 'br';
	if (!isset($options['popup-my-style-block'])) $options['popup-my-style-block'] = '';
	if (!isset($options['popup-my-style-header'])) $options['popup-my-style-header'] = '';
	if (!isset($options['popup-my-style-content'])) $options['popup-my-style-content'] = '';
	
	if ($options['popup-position'] == 'bl') $style_pos = 'bottom:15px; left:15px; width:22%;'; // bl||Снизу слева
	elseif ($options['popup-position'] == 'tp') $style_pos = 'top:15px; right:15px; width:22%;'; // tp||Сверху справа
	elseif ($options['popup-position'] == 'tl') $style_pos = 'top:15px; left:15px; width:22%;'; // tl||Сверху слева
	elseif ($options['popup-position'] == 'wt') $style_pos = 'top:15px; left:15px; right:15px;'; // wt||Во всю ширину окна сверху
	elseif ($options['popup-position'] == 'wb') $style_pos = 'bottom:15px; left:15px; right:15px;'; // wb||Во всю ширину окна снизу
	else $style_pos = 'bottom:15px; right:15px; width:22%;'; // br||Снизу справа


	if (!isset($options['popup-btn-close-color'])) $options['popup-btn-close-color'] = 'gray';
	
	if($options['popup-btn-close-color']=='red') $close_color = 'background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABcAAAAXCAYAAADgKtSgAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAABOtJREFUeNqMVV1oHFUUPnNndvYnm92YuLT2T0NTbWujD0qDCtafBh8FoS++iAT7oFFBrCiVFEyLlCK1pZZW8cWCIEIpiCgUFWulrRD/qqIYumQTErub7O7s7M7Pzsy9fvdOsm2JrV043Ey455tzzvd9ZzQhBN3oZx987wEcgxRGd4kgyFK7XRXtdlG0nHM9h/ZfvFGu9l/g9oFDutad3Ulc7Baut1o4LcJJoh0QcY4sjQh5wvf/EnZzX+9Hx0/cFHjz8LFBSiROimZrICpXiM/Pk7ABHgI44vElxkgzdCIjQaQzIt+f4HXrqcJXn5WuC26/e3RYY+xkVJ7PRtMzxBeqsrrFm1qnYlU9x4lHFEKarqMzt8KtxpMrJs6cWwZu7z84SIZxgf9TTodTJcLF+IJhoEIDAOzKOGQHYUjgAGeEW/H/hGVVooY9tOrSr8UOuPXmuMmy2b95tbouvDRFvNFQ1VAySZqZiF8gn5cqjyKMKYw5QGcgOH5uNMlttSaE5w31l/6I2GJ1LwNwXTgz2wHW0imyooCmrTrN2g2yMQqtK6OijvnPWBbNtprUwMs004xzkiaJhHGfq7OnFTX1l14zMdddfH6BRN2Kq0PFHk6xZTPdc+oT2nDsEDn5LDVQYc11IczNtOXkx7TxxAfk5LIUYe5ydIQuGf72dTamwAXnw1BGgVfrqk05AjmKFtrse2I7MXSQXL2KBvbuISfbRdrdm6j/jVdJRwfmyhWUe3CIPIGuEvH4GIIzfeD82o2DBkyxlYMU4Tgx+xIc0YUWa999Tz0PP0RQECV6b6ENb+0GaFfMAX4hSLd//IUKmS4iz4slKrkB+W2NtjHh+QOQkSJEjkSpAhcyAKHJIpWOHCfBY30b+fwV4IZNk3v2UgaKMSTpMo8tBnBA9YAEz0vGlUGWtLwYvYVbif/+Jy2c/nqZ+6aPf0gpqKOnp+eaHIFDlgL9ZBnG4Qq/HRtDyuyqqFbmidb3U9/jjywDXzPyDLkpkyyo5uocKe02sAIhXAYyixJctY5QBoGOPXDA166mO14Z7YwictzOiCQHA+Nj1ISBIoxG5UlQmYsIOC8y3myeFZ4bO066TZ0h2SCrb/ujy2Z8NQeJvl7Kbb2fnFZL5UUIBy9qwgcR5z8wbLXTcFYTrlLLSVpaSjIDUmtnzlLUcsjDnpkcG6fkQo2iiZ9p6p3DSin+7BzZEz8RfKzyfOkD35PgJY3zc8r+0+vvfRuOfJ1lMmRk0tB2Wjm0LquAhnWNUS6fo+7ubjXXeq1GdczawCiyukHdUh0YYxnunrRq1HDdF5+dKx5RPQdBsI8zNqIzVpBSMnBZ7oU8nJpPGMp1EDvhA6HA8wDM5fKd3RJi11sAl+ug7vuTyYi/f81W/O32zY+JpPlFKpU2M6jeSKWIyZ2xaO3rbcUQ4BZeWrItmm41XR4EQ89dnrq4bJ9fWLdpRJiJo5lUyuxOpSmFynVla71jjqV9LsnzfLlrHJpBxXOu44ZBsOP5y1OfX/dL9M2aO7eFhvFp0kwUsmaSMqg+gcp1pqv1wHFfyq2FqhegsjKi1vaLWhjteKFcmvjfb+jp29bnm0zbxXV91ND1vAnlGEu2RtUOwKXcEJUoig6kuTiyszLt3tQHeul3amW/DgqHA00bDjXKouoCnFCBhqvI+3K0XPr2Rl//fwUYAIJqBdt5wPuYAAAAAElFTkSuQmCC) no-repeat;';
	elseif($options['popup-btn-close-color']=='orange') $close_color = 'background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABcAAAAXCAYAAADgKtSgAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAABNlJREFUeNqMVVtoXFUUXffcx8y9mWSsNbGQGqQG+yHxRzQoSKASRX+ESj/qR7VUBKFFUWqF+paiKFqUkg9FsNQ/pX9qwQdKg7FiKFj8KNWmpg9jJplkZu77nof7nJumhtjYOzk58zh77bXXfhxLKYW1nuzH9+6mbQhKbIYoakrkTYhsSuXRhP/QB6fWsrX+Czwbf9u2Kt1PQqn9iif9BATwBBA5fSVh0UvRCzw7rfLOgWDrkSPXBJ799P6QZXtHyWhQhbOQ8RyQd4h4ARBwacXozwH0ovfkdFKli1u7Hv9u+qrg2cTBUTp8VEWNmmxNQ8XzZJjRL/oMgVgWvVWlE730Z+bSsoEiaZCDh2tPnZxYBZ4df2uImJxQ4YwvF89BZa3yhDF2CIcArNKPUgKQnBwX5a5FImcETg7awz3PnptaBk+/3e9ZXu2MSpoDcuEsHWqVIdsVkES06/DtfzEncMFpy8vIdC4kOco6SOJoEjwd7t13XrCSnfO0ytoDsnWBgNslU8dHnCvMtyIstmOkRNByu2B5XYgzifk2fR+mSDg5JAImMtql5d4RcftRA5t8ucejrO9VlDgKqwydGBeKQfUNYePOo+h7ZAyZcx2SjCNKiGXf7eh/7HPcuP0wMruuAUv5CNx2XGTCftmAUzyjKg97Vdw04elDWoq0EKhtvp/YVuHU+9H34OvkoAfovQ033LcPzAvgdG+Af/M9KHQR2a6pIIskFGCDv764ccgBz+9SlBRVxCVro7WDalBFdHYcwaZ7TbnZwfXoe+AVsEpXWYb0SJIwvXgSPZWAiCUmL2XiGbG3RpgS6aBpEJN1tvSjDa8awFo4g/nxseX6tv36CuDZY6+igoz4uMugYBqDgQIfZJTZOmleVoCuBs3eMv9Qq6+HavyG8PQ3q7qvOfERXN5CUOtZYaeLSdDKSVWm8jgh9kuNocp+MbWvELYoD+tuQdetW1aBrxveidyiioo6K+wkrZxLFFwlBB5Oke6mCbQD0yC08jSB7B7A+pFnrkhRJFck0jmgJKecQVIzaTuNwQk4Idq5kFMMeTiuljTXiS07jyON26gR45Uav7YyB+TAH7iTiMTGTvACcVogpKYQQv7MqHm+VlknJO3L4aRZUElWHIb4j+OQeYxi8bxJnps1oP76BfPfHyRnLfD2JaQXJuEyGNssz9GkxgoTPk3xTJj2X3h305vK9V9grg+HyspyA9OhEYWXUgfazKLSrMGnpcWNOi0kpDVTHFVCrjoKBbGfbbZx5lIL7TDZs/3jmUMm5rzIDwjBdjmS9eoydFBOhcCtIKjqpnJNNagiMkkLPAuBHSzPFp4naIURLs5HWAiz36uW+HDFVJx6Y2CLZN5XVd/3Ap8i8KpUvxXTeVebiloKnmcG+M+/O5huRAkld3jHJ7OnVs3zUy/dtEsxbyzwK1534KNarZhZYV2+FJamoq4KqZOeZWi2E1yYI9bNJOFFsW3XkdkvrnoTnXi+f6SA81nF83q7A49k8eA6DmybmcbSdVxwQQOswFwnxcxCSlLkU5bi2574tDH5v3fo+HMb6mHO9grYux3HrnsO5WEJvBCSRrFAJ+G6KhpCiHcCVx7acXguuaYL+vJzbHefHRfWaCGsUS6tmlCqV0o0hJRNsjtGTH9Y6/b/R4ABADTN8lCwGX+dAAAAAElFTkSuQmCC) no-repeat;';
	elseif($options['popup-btn-close-color']=='green') $close_color = 'background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABcAAAAXCAYAAADgKtSgAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAABPtJREFUeNqMVVtsVFUUXfc5dx59RUoCJOpHQ4FQeUUIPzTBED+NRkw0SKIkGlADMSFBG/2C0ET9MBQE5AMsX6J8IC+DH5gYGh8YKf6AhZKSIjLTmbl35j7Pfbj3GWwklcqd7Dkz5569zj5r7b2PkmUZZnsOnT24loa+JE16RRKXRBxVozga90J/ZPfm3Vdn81X+C3z/6f1aySq9kWXpQCCCBV7ggUZEsUCapVChIKNPJKJrzaC5Z9/WoeFHAj/y7ed9hmacdAO3p+xUUHWmQL9BUUtgflRFgabp0FUdmqoiFNFlx7Nf+Grg64mHgh8+d2iDqqonK85U6c7UJKqNKkUbgZcwINjoD2/CptDH0A16p9HJ/LLjOc9d2PPdyAzwoW/29Wmq/mPZvpe/Xb4Nx2/IeUPj6DSoZAQNXp2mCUgDCKIpTmM5mdBmBF5ueM6anz/9ZVyekL8Gvxw0aZPTZbucn7g3AdtzKBoVedMiy6OQK6KYK6BotUb+z/P5XB6mnqMDtU5Ep+smsk6s3LZCmwbXNG17w288/mf1DmiUkVoELHyBeqUOp2ojiRICLUgTQSTnG7UG0iglakzpY9Koa/oqJVVfkeADxwbMSIQ7mV/bsyWvOcOEQtotWrAIwx8cx8dvfYKSUULoh/CaPs0vxrGBL/DZuwdR0oswSFgClfxLDTLlQwlOwmxwA6+73qwTh7HkmLIFIhRYv+oZeYJ5j83D+5sHUNAK6F2wEDte2oGCVcDcrrl4eslqpHEqQfX7mxClPctee6pPp6JYHVOa+ZEvBeTj6QTe1mZi5PcRrF26VvLf1daFXa++J0EZgB8SEKNjoygUigjjgERXW+LTeqToV0MR9gQiBG/A6cZZwblbIodb98Zx5NSR6fxuL7ZPAze8BgaHB5EoMUzDkH4MqpAvj+TSw+AdZJRaqeSb043VZ5szpxvXJq/h4q8XZ1Tf0bNH4QoXnZ2drfUysVsjZ3eWZCXVCz2fBKXoEjmbyZeZtEqlgifnPoF1y9fNAN/07CaYioG6bbfWo4WapVRkSUpj6qsk5nirCu9XniyQFK7vYX7XfGx9fts0FX7oT1PEGrDImchkMbEfv0sS8hcxbzBO4M0fgiiQnMsXtChOBBzHRv+K/gc5Pj74gAa8wcrelXBdt+VH2RaFkcw0Av9Jpa52gazJogoC5Sh41A0dl65eAnfEyfIk9g7vRd2vY/TWFRw4uR+O6+Du1F1c+eM3KJoi/UICDlzqnoGYIA5GZG9Zs331XsuwdllUzgUrT7mdl/kd+UQXVSA7t7d1oL29TdJXrdVgE9cEACOnQzFU+BSEXbNR+6sGz/XfuX7q+pA8cxRFe5RU2UJad8t0UqT2yFlUzkVDFhWnJ4kvwQ3LQKfRIU9I7RZBGMBtumjWmhz5GKl3+IGuuOLN5etNzTyXt6glFSjynCV7BVfebF0xjEIJbFccNKoNn4pyzdiZG1dn9PNlry/bQlEesCwipmBR5FarrLX7Vfevfs7iMcde00Oz2oBru74QYuPYuRtnHnoTLd20tF9XtBOmaXbn8jmYOVOKy6XNdHEex5RV3Bl9amKe4yP0gnE6ycYb529e/t87dMnLizuorndSIb+t61qHplPkWosWLpBYMLjglCsnSfxRpmHo5tmb/iNd0P88vS8u1BBjAxHNViLrpsjLaZpV6fI+T5F+P9vt/7cAAwAk0f+4Vea/LQAAAABJRU5ErkJggg==) no-repeat;';
	elseif($options['popup-btn-close-color']=='blue') $close_color = 'background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABcAAAAXCAYAAADgKtSgAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAABQNJREFUeNqMVWuIVVUU/s77Po5zzfGCmk1BA0E0EPQYQkiw7o8KCyVDi+iHWNGD/iQVPn6lZlaWjBbTjwIhKUsJjIqwGhyxpiTRfiRJk5PZzNy5d+7rnH3O2fuc09p7bjdl0tyXc/e956z1rbXX+r51tDRNcbn12icn76CtL06SG7hI3UjE1ZDHo14gjg08teTU5Xy1/wJ/5aMTxpyc9Tg92sBCcTUBgUUCEU8Q001d0yD9KMjphs+3fPDCsr1XBL7z4Kk+29QPtBjvnagxlOsBmoxDxASczNhKcNPQYZma+k1Bjte8aOXQq8vHLgn++oGTJUPXDkzWmDs26WGqEUhHpAqQjOmT0iehIDIO4cKiIIahgYVxueaFD5zc8+CxWeBbP/ypj7L5frzqZ3+faIEM5WOVnczSICQJJs1laeRJuEhoTzsBKfty3Yv6z+19ZFSdUH699P6ITfaHJqZZ9rfxJqYJmE6AnGMg71hwMxbyWbrau/pP93OOCcfS2z1Q5SpaWrx/4er3jA64qevPNfyo549yC7TTfw1Z20QceqhXxtGcnkTKGVwFbEIEZFcdh1crIxUM1CNVGrnbhnaLCf6wAn9691E74PF62Tg6liqFYxlUL46+a7twaPsaDK6/DwU7RsRaYF6D7hfw6bbV2Ld5Bd1PqO4ztbfph0WXnorNCjxJ05LHeLHaDBFRDWWNbdOAiALc09+LLB19cbEL254swSWgm3oK2PjYnapEC7pdLOm7BmnMyU+n3sj+GFTStHfhip19JrHhdtkUPxSqsTMU0+HMcTF04iyW3nwddCrTvK4sXl53F5XGVjZy1b0Ax0+fRz7ngvFUNV32SvZAS8RSncB7pUBk9yXdpIEEc/M5nPmrhV0ff6eYINdcN9MBblDTNw5+DcqZSmF1/PQ2OB2nVw+iuBBGsRKI5LGm+Cw5rKE4vxs/n63hy5Ezs9T39sERNIitc+de1baf0YFaaQIkwtVJ2owaqrJTH0pS5in5X65U0LvQRem262eBr7v/VmR0gVqt1rZH2z8hLEH4gumtgI/KRlJjVYC4vXs+Q093Bs+vWdIphR/wTolkD7Y+cTe0OADnouMXC4GEh0hiMarT3Bim4aSUJpJ/lVdvNFXGF9Z407uHL+pBdyGH/hsXUyJ+2y9WLBMRI5tkRG/60VdNn7cY1V0+lMA0VmFYDr4ltniU7dhEHRsGD2PKS/DjrxXs2HcU9VaAP6ea+OGX80Roc8YvChH6DfCQjVH8Y2q29Dy6dxsp8sUcqS+XsZWspUI5KRGkQE030NXVpS5pX52eRr1ep1IQt+0s8TcDj0Vo1qZQL58joTWfLX/zxoApj8c532KkYq2hWUVJI0UlWo6Th5Wfo0SlEcVoDKvmmY6LrnlZJTo5NVnI4XtNePUyIr9xJoE5eNFUXPTQO8scU/s8m8nY2VwOGcdWY2BGeZeeioECbqBVkbNmgiQT91eG3jo1a54vWvnmWho8e5xsjgLkKfMMgRsKXIrjwnku4hhhGIK16vBrk2CNKRZxsapyZOCzS76JFizfvtTUk/227RTtTA62k4VpWtApgNbmv6QbJ0YEXg1hs4qQNUcp1qrK8O7j//sOLd67pWAk4XpdS58xTbNgSHDdaIuP1MyJbiEjyvnlWMQ7Et0eqB7Zxa7oBf3Pml/aZGhJVNLSuESoLl1FUmCZAlTJ74vK8J6hy739/xZgAFP6BdRXBC5PAAAAAElFTkSuQmCC) no-repeat;';
	else $close_color = 'background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABcAAAAXCAYAAADgKtSgAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA8hJREFUeNqMVUtIG1EUfZnEaCR+EANCIatAQQguKg1dCQVxowSqgnYhihJctHQVKBS6KxW6cBMqVDcighiMuigtFMFIQVoRBFehgUBWSjT+ovEX7TmP3GGCNfXB5c28ufec+31ju729VeVWLBZ7hs1/c3Pz+Pr62g3JXl1dpS4uLtZHR0e3y9na/gUejUbt1dXVIXx7B5BHEEUBsAKJstlsWg8kibOzsw/hcHjmQeDLy8t+h8MRy+fzvsPDQ3V0dKTwrAqFggbWRgC32+1aDMMgyebp6emLsbGx9L3gi4uL7VCOAdSdyWTU8fExDU1ACvVJwt1KgsgyIAhGIpH1O+Dz8/N+KP06ODhw7e7uKijqc0Shja3gFEbCNHHnIiFsSBCYnp5OmeB4cbpcrj/w1Luzs6OQRx1uRUWFBqfwXRaBCExhZEJCO4Bv4iwwOztb0BYwfIND797engnsdDp1Eff391U2m1WXl5eqqqpKVVZWqvPzc33OmhBcyOkMonwCh1/qqCcmJpwwCJ+cnJipoBIj8nq9amhoSOce0WlQesnz/v5+7cj4+LgGZjQkoS0a4D1gZgwctsPIQ3AaShroaSAQ0J56PB4VCoW0IYEHBgZ0FA0NDcrv95t2UlzUxtfd3e134MNT5osp4OJHKtbU1KitrS3V0tKiPautrVUjIyMKtdE6XIw0kUgozIQmYMGpWyx8mwEPffSSBNIRVKABi7uwsGD2t9vtLgGemprSz6yP2Ak4bHwGClJHcJk8mT7ujY2NKp1Oq42NjTvTt7S0pKOtr68vmQOu4iy4DeQ7z4rLYEjfc2f3NDU1qdbW1jvgXV1d2ktOsOiLEAuSJ3iK+bJOHneOPD3v6+szU0FPJUWsAYssvS528o40pwyA/KSR3B0yeWw/emzN8eTkZEkNSNDc3Ky/iR1TXMT7baBXf4Agx0OZOgrbjt3C3uZ1wOKxr5PJpJqbm9OATBu7hQ4IMM8BzgtsXY//4ODgR1T8LXtappAdIEMjrcj2pD7uHzPX1GPrMo2cWF54IHi9srIScVABjB9gNIxHj7XqJGFLMgoSkIyLDhBQ8ss00GOCI7okVL6U3Ioo3HMYfIOhk4AElour3K0owMW7hp0XWF1d3b5zn/f09AwD6DOmsITAOhzSFTLVuVxOFX8qeZD1xuPxr/f+iYLBYBuAosilR/Iv3ks/W4EpSEUKhL1ra2ub//2HdnZ21kE5DE9fAbTOCk6vJc/8+4DoE5yJwOP8g37Qsjo6Ouy8NaFDcUM8kAzOsti/w9N4ub//XwEGAN8IKuohXMXvAAAAAElFTkSuQmCC) no-repeat;';
	
	// точка отсчета top - верх  bottom - низ
	if (!isset($options['popup-xy'])) $options['popup-xy'] = 'bottom';
	
	if ($options['popup-xy'] == 'top') 
	{
		$if_xy = '$(window).scrollTop() < ' . $options['popup-bottom'] . ' && !popupBlock.is(":animated")';
		$if_xy_top = 'true';
	}
	else 
	{
		$if_xy = '$(window).scrollTop() + $(window).height() > $(document).height() - ' . $options['popup-bottom'] . ' && !popupBlock.is(":animated")';
		$if_xy_top = 'false';
	}
	
	if (!isset($options['popup-btn-close']) or $options['popup-btn-close']) $popup_btn_close = 'false';
		else $popup_btn_close = 'true';
		
	
	if (!isset($options['popup-allways-view'])) $options['popup-allways-view'] = false;
	$popup_allways_view =  $options['popup-allways-view'] ? 'false' : 'true';
	
	echo mso_load_jquery() . mso_load_jquery('jquery.cookie.js') . '
	<script>
	if (document.querySelector)
	{ 
		$(function() 
		{
			if (' . $popup_btn_close . ' || !$.cookie("hidePopup")) 
			{
				var popupBlock = $("div.popup-block"),
					close = $("span.popup-close");
				
				if ('. $if_xy_top . ') {popupBlock.show();}
				
				if (' . $popup_allways_view  . ')
				{
					$(window).scroll(function()
					{
						if (popupBlock.data("hide")) 
						{
							popupBlock.hide();
						}
						else if (' . $if_xy . ') 
						{
							popupBlock.fadeIn(' . $options['popup-fade'] . ');
						}
						else if (!popupBlock.is(":animated")) 
						{
							popupBlock.fadeOut(' . $options['popup-fade'] . ');
						}
					});
				}
				else
				{
					popupBlock.show();
				}
				
				close.click(function() 
				{
					popupBlock.hide().data("hide", 1);
					$.cookie("hidePopup", "true", {expires: ' . $options['popup-cookie'] . ', path: "/"});
				});
			}
		});
	}
	</script>

	<style>
		div.popup-block {position:fixed; display:none; z-index: 999; padding:8px; border:1px solid #ccc; background:#fff; border-radius:5px; box-shadow:1px 2px 5px #999; ' . $style_pos . $options['popup-my-style-block'] . '}
		h3.popup-header {margin:0 0 8px; padding:0 40px 8px 0; border-bottom:1px solid #ccc; font:17px Verdana, sans-serif; ' . $options['popup-my-style-header'] . '}
		span.popup-close {float:right; width:23px; height:23px; margin-bottom:8px; ' . $close_color . ' cursor:pointer;}
		div.popup-content {clear:both; font-size:13px;' . $options['popup-my-style-content'] . '}
	</style>
';
		
	return $args;
}

function popup_body_end($args = array())
{	
	$options = mso_get_option('plugin_popup', 'plugins', array());
	if (!isset($options['popup-content']) or !$options['popup-content']) return $args;
	if (!isset($options['popup-header'])) $options['popup-header'] = t('Интересные записи');
	if ($options['popup-header']) $options['popup-header'] = '<h3 class="popup-header">' . $options['popup-header'] . '</h3>';
	
	
	if (!isset($options['popup-btn-close'])) $options['popup-btn-close'] = 1;

	$popup_btn_close = $options['popup-btn-close'] ? '<span class="popup-close" title="Закрыть"></span>' : '';

	echo '
	<div class="popup-block">
			' . $popup_btn_close . $options['popup-header'] . '
		<div class="popup-content">' . $options['popup-content'] . '<div>
	</div>
	';

	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function popup_mso_options() 
{
	if ( !mso_check_allow('popup_edit') ) 
	{
		echo t('Доступ запрещен');
		return;
	}
	
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_popup', 'plugins', 
		array(
			'popup-header' => array(
							'type' => 'text', 
							'name' => t('Заголовок блока'), 
							'description' => t('Укажите заголовок блока'), 
							'default' => t('Интересные записи'),
						),
						
			'popup-content' => array(
							'type' => 'textarea', 
							'name' => t('Текст блока'), 
							'description' => t('Укажите текст блока'), 
							'default' => '',
						),			
			
			'popup-position' => array(
							'type' => 'select', 
							'name' => t('Положение блока'), 
							'description' => t('Выберите расположение блока на сайте'), 
							'values' => 'br||Снизу справа # bl||Снизу слева # tp||Сверху справа # tl||Сверху слева # wt||Во всю ширину окна сверху # wb||Во всю ширину окна снизу',
							'default' => 'br',
						),
						
			'popup-xy' => array(
							'type' => 'select', 
							'name' => t('Точка отсчета для задания положения блока'), 
							'description' => t(''), 
							'values' => t('bottom||Низ страницы # top||Верх страницы'),
							'default' => 'bottom',
						),
			
			'popup-bottom' => array(
							'type' => 'text', 
							'name' => t('Расстояние от точки отсчета, на которой появится (если низ)/исчезнет (если верх) блок (px)'), 
							'description' => t('Вне этой границы блок не отображается'), 
							'default' => 500,
						),
						
			'popup-fade' => array(
							'type' => 'text', 
							'name' => t('Время эффекта появления блока'), 
							'description' => t('Указывается в миллисекундах'), 
							'default' => 600,
						),
						
			'popup-cookie' => array(
							'type' => 'text', 
							'name' => t('При закрытии блока не показывать его в течение'), 
							'description' => t('Укажите срок в днях'), 
							'default' => 30,
						),
						
			'popup-allways-view' => array(
							'type' => 'checkbox', 
							'name' => t('Всегда отображать блок'), 
							'description' => t('Если отметить, то блок отображается всегда, без учета скролинга страницы.'), 
							'default' => 0,
						),
						
			'popup-btn-close' => array(
							'type' => 'checkbox', 
							'name' => t('Отображать кнопку закрытия блока'), 
							'description' => '', //t(''), 
							'default' => 1,
						),
						
			'popup-btn-close-color' => array(
							'type' => 'select', 
							'name' => t('Цвет кнопки закрытия'), 
							'description' => t('Можно подстроить под дизайн своего сайта'), 
							'values' => t('grey||Серый # red||Красный # orange||Оранжевый # green||Зеленый # blue||Синий'),
							'default' => 'grey',
						),
			
			'popup-my-style-block' => array(
							'type' => 'text', 
							'name' => t('Свои css-стили блока'), 
							'description' => t('Укажите свой вариант оформления'), 
							'default' => '',
						),
						
			'popup-my-style-header' => array(
							'type' => 'text', 
							'name' => t('Свои css-стили заголовка блока'), 
							'description' => t('Укажите свой вариант'), 
							'default' => '',
						),	
							
			'popup-my-style-content' => array(
							'type' => 'text', 
							'name' => t('Свои css-стили текста блока'), 
							'description' => t('Укажите свой вариант'), 
							'default' => '',
						),			
			),
		t('Настройки плагина PopUp'), // титул
		t('Плагин выводит всплывающее popup-окно на страницах сайта.')   // инфо
	);
}


# end file