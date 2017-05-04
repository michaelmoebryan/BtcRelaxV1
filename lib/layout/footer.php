<?php
	use BtcRelax\User;
	use BtcRelax\Core;
	global $core;
	$sessionState=$core->getSessionState();
	$userHash=null;
        if (!isset($extra['error_message']))
           {
                if($sessionState==BtcRelax\SecureSession::STATUS_USER || $sessionState==BtcRelax\SecureSession::STATUS_BANNED){
                                $cUser=$core->getUser();
                                $userHash=$cUser->getUserHash(); ?>
                            <script>
                                $(document).ready(function (){
                                        updateSessionState('<?php	echo(sprintf('%s',$sessionState));?>');
                                        });
                                        var Tawk_API=Tawk_API || {}
                                        ,Tawk_LoadStart=new Date();
                                        Tawk_API.onLoad = function(){
                                Tawk_API.setAttributes({
                                'bitid'    : '<?php	echo($userHash);?>',
                                }, function(error){});
                                        };

                                (function (){
                                        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
                                        s1.async=true;
                                        s1.src='https://embed.tawk.to/587f914bbcf30e71ac11504b/default';
                                        s1.charset='UTF-8';
                                        s1.setAttribute('crossorigin','*');
                                        s0.parentNode.insertBefore(s1,s0);
                                        })();
                                </script>
                <?php	}else{	?>
                            <script>$(document).ready(function (){
                                        updateSessionState('<?php	echo(sprintf('%s',$sessionState));?>');
                                        });
                                        var LHCFAQOptions = {status_text:'FAQ',url:'faq.php',identifier:''};
                                        (function() {
                                        var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                                        po.src = '//fastfen.club/helper/index.php/rus/faq/getstatus/(position)/middle_right/(top)/0/(units)/percents/(theme)/1';
                                        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
                                        })();	
                                </script>
                <?php   };      
           };
?>