<form $FormAttributes data-layout-type="border">
    <div class="cms-content-fields center">
        <fieldset>
            <% if $Legend %><legend>$Legend</legend><% end_if %>
            <% loop $Fields %>
                $FieldHolder
            <% end_loop %>
            <div class="clear"></div>
        </fieldset>
    </div>
</form>
