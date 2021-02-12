<!-- // MOD 087.6: Add custom template file. -->
<table class="table" style="width:100%; margin-top: 50px;">
	<thead>
		<tr style="border-bottom:1px solid #D6D4D4; font-size: 12px;">
			<th>Row #</th>
			<th>Ref</th>
			<th>Qty</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$rows key=row_key item=row}
			<tr style="border-bottom:1px solid #D6D4D4; font-size: 12px;">
				<td style="padding: 10px 5px 10px 0;">
					{$row_key}
				</td>
				<td style="padding: 10px 5px 10px 0;">
					{$row.reference}
				</td>
				<td style="padding: 10px 5px 10px 0;">
					{$row.quantity}
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>
<!-- MOD 087.6 END -->
