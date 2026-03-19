


            <?php
            if(request('post') == 'vps')
            {
                ?>
                @include("orderitem.glxv3.post-vps")
                <?php
            }
            else
            if(request('cat') == 'vps')
            {
            ?>

                @include("orderitem.glxv3.vps")

            <?php
            }
            else
            if(request('cat') == 'server-hardware')
            {
            ?>

                @include("orderitem.glxv3.server-hardware")

            <?php
            }            else
            if(request('cat') == 'email-service')
            {
            ?>

                @include("orderitem.glxv3.email-service")

            <?php
            }
            else
            if(request('cat') == 'gpu-server')
            {
            ?>

                @include("orderitem.glxv3.gpu-server")

            <?php
            }
            else
            if(request('cat') == 'web-hosting')
            {
            ?>

            @include("orderitem.glxv3.web-hosting")
            <?php
            }

            ?>
