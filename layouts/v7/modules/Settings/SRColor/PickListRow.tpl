{if $RESULT neq ''}
{foreach from=$RESULT key="key" item="VALUES"}
<tr>
	<td class="fieldLabel">
		<input class="inputElement" value="" type="hidden">
		<label>{$VALUES['value']}</label>
	</td>
	<td class=" fieldValue">
		<div class=" col-lg-6 col-md-6 col-sm-12">
			<input class="inputElement" value="">
		</div>
	</td>
</tr>
{/foreach}
{/if}
