<?php
$config = array (	
		//应用ID,您的APPID。
		'app_id' => "2017111409928739",

		//商户私钥，您的原始格式RSA私钥
		'merchant_private_key' => "MIIEpAIBAAKCAQEApTUPVaij7+TPtU0/AeGFfTrN6ZdZ6v0W2omC1+DLijcmildg fjf8DDE76f74xfX30YANFOdKE3o6bEdG7LalVMf0L/RbOcKUS1MNMwlpkaFS2/CX Ynd6T753KUQry9NVFR6kvdJDpP4WO+btOLRKy1+GUubZdhw0T/sr6s8aErkNy+rF MdkQnsjJdFpdgKj1nzIzTcvj8RBgt5VAqcUUmv7GnUGIOqlUlu7oyrTjoo3uaabI V5OWt3EopYUBBZMO259nM0SJX07ASizk2/K0JfIQpCPh2rug01XfZ9eiV3b/LmY9 bdVxFuLcr6LdyNwfaTV02R/dVnrOLRSBBQ0PQwIDAQABAoIBAQCPKkkMqSTP6hBZ hARbA+1jVYdFq2Q/sG2SmmHp5CNetmZOsmOrXaathijuoYdCPeIxCe/MMpbOBDkG xknfLnRd4R1qRS6dAlLyZ5ljpf93NT8R3A/EQ9eZrWukNjBh5NSxhamr2b/HBm2M IZVnc03xqelEhErlAJIQ0ZAAXtKwb4pHyIVjet4nvgGZA5EoCx5Bz9yepkDK26gl MpE3drwWAZZUy47XxkdLb3HxEDPOiIzZXuY0YQzJdFf5XDrZ/hzWBtfq1fsup3zu CS5Lu6iaIRaYMkRybd8DMIYAJg9OLVc7ogIuQ8Jiuv1Aq5jF/+G1BYnDLAU2e2IS 5Dab5qXZAoGBANoqrcroiGZl07YMr16Guo3SDRsAs9bDCWcnpEivozxxXTZGJh83 /wyxjjDrDG8b/XIABcQMT/lJL0ICYH99zS2rcqWtjmlSwqcKe/MyrAuuVMMvVpTm Zox2BaWfM4lhEOyyo9lCGQsLpeLw3NCf3QaDbySy/EnBKhfPWpGxseonAoGBAMHb St1khrw2zU3Bdot5sYGoQRJW/RuKXiyf/6cNUj6XBeD/W7CvKU8kGQJ7K2R4hGGg +pkiNTCdcT8ubSwIpA4usXpYIrRBhsuJEwqViZXbOGIZirBdyUGiUDKkBDl4YHxF ucYLB3FS/1kHQalIWS7T1CzHd4UOuYi3aH5sXO+FAoGASV9bLcfAv/d1GV/wxvC2 4yWGxNMaqJrVmbzKUqvHUXeq6qry/ULe40z/zlHuz5txRJrfVYzyhJtpamDURWxw yfBEUZYqNB/iWT18bFFZbWBHH5HXI6LNUGYNBiOhuI1NnN2Dn3jMZVuYgdeR3BQ3 yI4Bni0YDlIJrxJMmn1RobcCgYEAimmR/ZtOHEqgsdjlVFayZ9oDhB26IJTeAszG k7cONwRLvUd2ZyPZwdkLRls9M0JdevuekgH7qldvyWXTqzIMONgb/je5p6x1mxOn FdKJZwccLecAwEZmcUd8LXwwS/xoH7MFHqM3UDGrghyNRFoU2zuB7esJqmZGEJir skCa5qUCgYAq2JQ87alGcRUXVPQoQgy332kiarl7GZSnVbPJFHPRUYvET5GBw2iW 0009euXH6FKewAy82304m4ghzMRCUK+A6yvhINoNHJUZxlBsfjI00mxrWZ8xCi7V Z9W1tH7t6QGnWAyOz+tHMNkZF1zd4BNlkrBp4yK5Eno9ZraHImYF/g==",
		
		//异步通知地址
		'notify_url' => "http://vyskin.com/Alipay/page_notify_url",
		
		//同步跳转
		'return_url' => "http://vyskin.com/Alipay/page_return_url",

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEApTUPVaij7+TPtU0/AeGFfTrN6ZdZ6v0W2omC1+DLijcmildgfjf8DDE76f74xfX30YANFOdKE3o6bEdG7LalVMf0L/RbOcKUS1MNMwlpkaFS2/CXYnd6T753KUQry9NVFR6kvdJDpP4WO+btOLRKy1+GUubZdhw0T/sr6s8aErkNy+rFMdkQnsjJdFpdgKj1nzIzTcvj8RBgt5VAqcUUmv7GnUGIOqlUlu7oyrTjoo3uaabIV5OWt3EopYUBBZMO259nM0SJX07ASizk2/K0JfIQpCPh2rug01XfZ9eiV3b/LmY9bdVxFuLcr6LdyNwfaTV02R/dVnrOLRSBBQ0PQwIDAQAB",
		
	
);