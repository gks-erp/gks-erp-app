<?php
define('GKS_QR_SAVE_DIR', GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/uploads/qr_codes/');
define('QR_CACHEABLE', true); // use cache - more disk reads but less CPU power, masks and format templates are stored there
define('QR_CACHE_DIR', GKS_SITE_PATH.'gks_erp_qr_code/cache/'); // used when QR_CACHEABLE === true
define('QR_LOG_DIR', GKS_SITE_PATH.'gks_erp_qr_code/log/');
define('QR_FIND_BEST_MASK', true); // if true, estimates best mask (spec. default, but extremally slow; set to false to significant performance boost but (propably) worst quality code
define('QR_FIND_FROM_RANDOM', false); // if false, checks all masks available, otherwise value tells count of masks need to be checked, mask id are got randomly
define('QR_DEFAULT_MASK', 2); // when QR_FIND_BEST_MASK === false
define('QR_PNG_MAXIMUM_SIZE',  1024); // maximum allowed png image width (in pixels), tune to make sure GD and PHP can handle such big images
                                                  