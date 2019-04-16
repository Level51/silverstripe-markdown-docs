<form $FormAttributes data-layout-type="border">
    <div class="panel panel--padded panel--scrollable flexbox-area-grow cms-content-fields">
        <fieldset>
            <% if $Legend %><legend>$Legend</legend><% end_if %>
            <% loop $Fields %>
                $FieldHolder
            <% end_loop %>
            <div class="clear"><!-- --></div>
        </fieldset>
    </div>
</form>
