<div class="binnash-gridbox">
    <div id="binnash-facebook-public-photo" class="binnash-container">
        <div class="binnash-screen">
            <h3 class="binnash-title">Facebook Photo</h3>
            <div id="binnash-photo-album-" class="binnash-photo-album">         
                <table>
                    <tr>
	                    <td>
                            <div class="binnash-prev"></div>
                        </td>
	                    <td>
                            <div class="stripmask">
                                <div class="strip">
                                    <?php foreach ($urls as $url): ?>                                
                                        <div>
                                            <div style="padding:3px 6px;">
                                               <a popup="">
                                                    <div class="image" style="<?php echo $url['style'];?>"></div>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach;?>
                                </div>
                            </div>
                        </td>
	                    <td >        
                            <div class="binnash-next"></div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
jQuery(document).ready(function($){
    $('#binnash-facebook-public-photo').BinnashImageSlider({});
});
</script>

