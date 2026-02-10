<?php
use App\Core\View;
ob_start();
$statusMap = [
	'pending' => ['label' => 'Pendente', 'class' => 'bg-warning text-dark'],
	'approved' => ['label' => 'Aprovado', 'class' => 'bg-success'],
	'rejected' => ['label' => 'Rejeitado', 'class' => 'bg-danger'],
	'revoked' => ['label' => 'Estornado', 'class' => 'bg-dark'],
];
$currentUser = $currentUser ?? null;
$canManage = \App\Core\Auth::isAdmin($currentUser);
$historyByUser = $historyByUser ?? [];
$proofModals = [];
$formatDate = static function (?string $dt): string {
	if (!$dt) {
		return '-';
	}
	$ts = strtotime($dt);
	if ($ts === false) {
		return '-';
	}
	return date('Y-m-d H:i', $ts);
};
$formatAccountAge = static function (?string $dt): string {
	if (!$dt) {
		return '-';
	}
	try {
		$start = new DateTimeImmutable($dt);
		$now = new DateTimeImmutable('now');
		$diff = $start->diff($now);
		return $diff->y . 'a ' . $diff->m . 'm ' . $diff->d . 'd';
	} catch (Exception $e) {
		return '-';
	}
};
$isZeroDate = static function (?string $dt): bool {
	if (!$dt) {
		return true;
	}
	$clean = trim($dt);
	return $clean === '0000-00-00 00:00:00' || $clean === '0000-00-00';
};
?>
<h1 class="h4 mb-3">Pagamentos</h1>
<hr class="text-success" />
<div class="table-responsive">
	<table class="table table-hover align-middle">
		<thead class="table-light">
		<tr>
			<th scope="col">Usuário</th>
			<th scope="col">Pacote</th>
			<th scope="col" style="width: 90px;">Meses</th>
			<th scope="col" style="width: 140px;">Status</th>
			<th scope="col" style="width: 140px;">Comprovante</th>
			<th scope="col" style="width: 170px;">Criado</th>
			<th scope="col" class="text-end" style="width: 180px;">Ações</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach (($payments ?? []) as $p): ?>
			<?php
			$st = (string)($p['status'] ?? '');
			$isRefunded = !empty($p['revoked_at']) && !$isZeroDate((string)$p['revoked_at']);
			if ($isRefunded && $st !== 'revoked') {
				$st = 'revoked';
			}
			$stMeta = $statusMap[$st] ?? ['label' => $st !== '' ? $st : '-', 'class' => 'bg-secondary'];
			$proofPath = (string)($p['proof_path'] ?? '');
			$proofUrl = base_path('/admin/payments/proof/' . (int)$p['id']);
			$proofExt = $proofPath !== '' ? strtolower(pathinfo($proofPath, PATHINFO_EXTENSION)) : '';
			?>
			<tr>
				<td>
					<?php if (!empty($p['user_name'])): ?>
						<button class="btn btn-link p-0 text-decoration-none" type="button" data-bs-toggle="modal" data-bs-target="#userInfoModal<?= (int)$p['user_id'] ?>">
							<?= View::e((string)$p['user_name']) ?>
						</button>
					<?php else: ?>
						<?= View::e('#' . (int)$p['user_id']) ?>
					<?php endif; ?>
				</td>
				<td><?= View::e((string)($p['package_name'] ?? ('#' . (int)$p['package_id']))) ?></td>
				<td><?= (int)($p['months'] ?? 1) ?></td>
				<td>
					<span class="badge <?= View::e($stMeta['class']) ?>">
						<?= View::e($stMeta['label']) ?>
					</span>
				</td>
				<td>
					<?php if ($proofPath !== ''): ?>
						<button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#proofModal<?= (int)$p['id'] ?>">Ver</button>
					<?php else: ?>
						<span class="text-muted">-</span>
					<?php endif; ?>
				</td>
				<td><?= View::e((string)$p['created_at']) ?></td>
				<td class="text-end">
					<?php if ($p['status'] === 'pending'): ?>
						<form method="post" action="<?= base_path('/admin/payments/approve') ?>" class="d-inline">
							<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
							<input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
							<button class="btn btn-sm btn-success" type="submit">Aprovar</button>
						</form>
						<form method="post" action="<?= base_path('/admin/payments/reject') ?>" class="d-inline">
							<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
							<input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
							<button class="btn btn-sm btn-outline-danger" type="submit">Rejeitar</button>
						</form>
					<?php elseif ($st === 'approved' && $canManage && empty($p['revoked_at'])): ?>
						<button class="btn btn-sm btn-outline-danger" type="button" data-bs-toggle="modal" data-bs-target="#revokeModal"
							data-action="open-refund"
							data-payment-id="<?= (int)$p['id'] ?>"
							data-revoke-id="<?= (int)$p['id'] ?>"
							data-revoke-user="<?= View::e((string)($p['user_name'] ?? ('#' . (int)$p['user_id']))) ?>"
							data-revoke-package="<?= View::e((string)($p['package_name'] ?? ('#' . (int)$p['package_id']))) ?>"
							data-revoke-months="<?= (int)($p['months'] ?? 1) ?>"
							data-revoke-created="<?= View::e((string)($p['created_at'] ?? '-')) ?>"
							data-revoke-status="<?= View::e($stMeta['label']) ?>"
							data-revoke-tier="<?= View::e((string)($p['user_tier'] ?? '-')) ?>"
							data-revoke-subscription="<?= View::e((string)($p['user_subscription_expires_at'] ?? '-')) ?>"
							data-revoke-credits="<?= View::e((string)($p['user_credits'] ?? '0')) ?>"
							data-revoke-proof="<?= $proofPath !== '' ? 'Sim' : 'Nao' ?>"
						>Estornar compra</button>
					<?php elseif ($st === 'revoked' && $canManage): ?>
						<button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#revokeCancelModal"
							data-payment-id="<?= (int)$p['id'] ?>"
							data-revoke-id="<?= (int)$p['id'] ?>"
							data-revoke-user="<?= View::e((string)($p['user_name'] ?? ('#' . (int)$p['user_id']))) ?>"
							data-revoke-package="<?= View::e((string)($p['package_name'] ?? ('#' . (int)$p['package_id']))) ?>"
							data-revoke-months="<?= (int)($p['months'] ?? 1) ?>"
							data-revoke-created="<?= View::e((string)($p['created_at'] ?? '-')) ?>"
							data-revoke-status="<?= View::e($stMeta['label']) ?>"
							data-revoke-tier="<?= View::e((string)($p['user_tier'] ?? '-')) ?>"
							data-revoke-subscription="<?= View::e((string)($p['user_subscription_expires_at'] ?? '-')) ?>"
							data-revoke-credits="<?= View::e((string)($p['user_credits'] ?? '0')) ?>"
							data-revoke-proof="<?= $proofPath !== '' ? 'Sim' : 'Nao' ?>"
						>Cancelar estorno</button>
					<?php else: ?>
						<span class="text-muted">-</span>
					<?php endif; ?>
				</td>
			</tr>
			<?php
			if ($proofPath !== '') {
				ob_start();
				?>
				<div class="modal fade" id="proofModal<?= (int)$p['id'] ?>" tabindex="-1" aria-hidden="true">
					<div class="modal-dialog modal-xl modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">Comprovante</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
							</div>
							<div class="modal-body">
							<div class="text-muted small mb-2">Usuário: <?= View::e((string)($p['user_name'] ?? ('#' . (int)$p['user_id']))) ?></div>
								<div class="mb-2">
									<a class="small" href="<?= View::e($proofUrl) ?>" target="_blank" rel="noopener">Abrir comprovante em nova aba</a>
								</div>
								<?php if ($proofExt === 'pdf' || $proofExt === ''): ?>
									<div class="border rounded" style="height:70vh;">
										<iframe src="<?= View::e($proofUrl) ?>" title="Comprovante" style="border:0;width:100%;height:100%;" allowfullscreen></iframe>
									</div>
								<?php else: ?>
									<div class="text-center">
										<img src="<?= View::e($proofUrl) ?>" alt="Comprovante" class="img-fluid" style="max-height:70vh;" />
									</div>
								<?php endif; ?>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
							</div>
						</div>
					</div>
				</div>
				<?php
				$proofModals[] = ob_get_clean();
			}
		endforeach;
		?>
		</tbody>
	</table>
</div>
<?= implode('', $proofModals) ?>
<?php
$renderedUsers = [];
foreach (($payments ?? []) as $p):
	$uid = (int)($p['user_id'] ?? 0);
	if ($uid <= 0 || isset($renderedUsers[$uid])) {
		continue;
	}
	$renderedUsers[$uid] = true;
	$userName = (string)($p['user_name'] ?? ('#' . $uid));
	$userEmail = (string)($p['user_email'] ?? '-');
	$userPhone = (string)($p['user_phone'] ?? '-');
	$userCountry = (string)($p['user_phone_country'] ?? '');
	$userTier = (string)($p['user_tier'] ?? '-');
	$userCredits = (string)($p['user_credits'] ?? '0');
	$userSub = (string)($p['user_subscription_expires_at'] ?? '-');
	$userRegistered = (string)($p['user_registered_at'] ?? '');
	$userAge = $formatAccountAge($userRegistered);
	$history = $historyByUser[$uid] ?? [];
?>
<div class="modal fade" id="userInfoModal<?= (int)$uid ?>" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-xl modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Dados administrativos - <?= View::e($userName) ?></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
			</div>
			<div class="modal-body">
				<div class="row g-3 mb-3">
					<div class="col-md-4">
						<div class="small text-muted">Contato</div>
						<div><strong>Email:</strong> <?= View::e($userEmail) ?></div>
						<div><strong>Telefone:</strong> <?= View::e(trim($userCountry . ' ' . $userPhone)) ?></div>
					</div>
					<div class="col-md-4">
						<div class="small text-muted">Conta</div>
						<div><strong>Cadastro:</strong> <?= View::e($formatDate($userRegistered)) ?></div>
						<div><strong>Tempo:</strong> <?= View::e($userAge) ?></div>
					</div>
					<div class="col-md-4">
						<div class="small text-muted">Acesso</div>
						<div><strong>Tier:</strong> <?= View::e($userTier) ?></div>
						<div><strong>Assinatura:</strong> <?= View::e($userSub !== '' ? $userSub : '-') ?></div>
							<div><strong>Créditos:</strong> <?= View::e($userCredits) ?></div>
					</div>
				</div>
				<div class="mb-2"><strong>Historico de compras</strong></div>
				<div class="table-responsive">
					<table class="table table-sm align-middle">
						<thead class="table-light">
						<tr>
							<th scope="col">ID</th>
							<th scope="col">Pacote</th>
							<th scope="col">Meses</th>
							<th scope="col">Status</th>
							<th scope="col">Criado</th>
						</tr>
						</thead>
						<tbody>
						<?php if (empty($history)): ?>
							<tr>
								<td colspan="5" class="text-muted">Sem historico.</td>
							</tr>
						<?php else: ?>
							<?php foreach ($history as $h): ?>
								<?php
									$hst = (string)($h['status'] ?? '');
									if (!empty($h['revoked_at'])) {
										$hst = 'revoked';
									}
									$hstMeta = $statusMap[$hst] ?? ['label' => $hst !== '' ? $hst : '-', 'class' => 'bg-secondary'];
								?>
								<tr>
									<td><?= (int)($h['id'] ?? 0) ?></td>
									<td><?= View::e((string)($h['package_name'] ?? ('#' . (int)($h['package_id'] ?? 0)))) ?></td>
									<td><?= (int)($h['months'] ?? 1) ?></td>
									<td><span class="badge <?= View::e($hstMeta['class']) ?>"><?= View::e($hstMeta['label']) ?></span></td>
									<td><?= View::e($formatDate((string)($h['created_at'] ?? ''))) ?></td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
			</div>
		</div>
	</div>
</div>
<?php endforeach; ?>
<div class="modal fade" id="revokeModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Estorno do pagamento</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
			</div>
			<form method="post" action="">
				<div class="modal-body">
					<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
					<input type="hidden" name="id" id="revokeId" value="">
					<div class="alert alert-warning small mb-3">
							<strong>Atenção:</strong> este estorno desfaz a compra, removendo créditos e revertendo a assinatura deste pagamento.
					</div>
					<ul class="list-unstyled small mb-0">
							<li><strong>Usuário:</strong> <span id="revokeUser"></span></li>
						<li><strong>Pacote:</strong> <span id="revokePackage"></span></li>
						<li><strong>Meses:</strong> <span id="revokeMonths"></span></li>
						<li><strong>Status atual:</strong> <span id="revokeStatus"></span></li>
						<li><strong>Criado em:</strong> <span id="revokeCreated"></span></li>
						<li><strong>Tier atual:</strong> <span id="revokeTier"></span></li>
						<li><strong>Assinatura atual:</strong> <span id="revokeSubscription"></span></li>
							<li><strong>Créditos atuais:</strong> <span id="revokeCredits"></span></li>
						<li><strong>Comprovante:</strong> <span id="revokeProof"></span> <a class="ms-2 small" id="revokeProofLink" href="#" target="_blank" rel="noopener" style="display:none">Abrir</a></li>
					</ul>
					<div class="mt-3">
						<label class="form-label">Motivo</label>
						<textarea class="form-control" name="reason" id="revokeReason" rows="3" required></textarea>
						<div class="form-text">Informe o motivo do estorno para auditoria.</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
					<button class="btn btn-danger" type="button" id="revokeConfirmBtn">Confirmar estorno</button>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="revokeCancelModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Cancelar estorno</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
			</div>
			<form method="post" action="<?= base_path('/admin/payments/revoke-cancel') ?>">
				<div class="modal-body">
					<input type="hidden" name="_csrf" value="<?= View::e($csrf) ?>">
					<input type="hidden" name="id" id="revokeCancelId" value="">
					<div class="alert alert-info small mb-3">
							<strong>Confirmação:</strong> esta ação desfaz o estorno e restaura os dados anteriores da compra.
					</div>
					<ul class="list-unstyled small mb-0">
							<li><strong>Usuário:</strong> <span id="revokeCancelUser"></span></li>
						<li><strong>Pacote:</strong> <span id="revokeCancelPackage"></span></li>
						<li><strong>Meses:</strong> <span id="revokeCancelMonths"></span></li>
						<li><strong>Status atual:</strong> <span id="revokeCancelStatus"></span></li>
						<li><strong>Criado em:</strong> <span id="revokeCancelCreated"></span></li>
						<li><strong>Tier atual:</strong> <span id="revokeCancelTier"></span></li>
						<li><strong>Assinatura atual:</strong> <span id="revokeCancelSubscription"></span></li>
							<li><strong>Créditos atuais:</strong> <span id="revokeCancelCredits"></span></li>
						<li><strong>Comprovante:</strong> <span id="revokeCancelProof"></span></li>
					</ul>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
					<button class="btn btn-primary" type="submit">Confirmar cancelamento do estorno</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
	var proofModal = document.getElementById('proofModal');
	if (proofModal) {
		proofModal.addEventListener('show.bs.modal', function (event) {
			var button = event.relatedTarget || document.activeElement;
			if (!button) {
				return;
			}
			var url = button.getAttribute('data-proof-url') || '';
			var type = button.getAttribute('data-proof-type') || 'image';
			var user = button.getAttribute('data-proof-user') || '';
			var frameWrap = document.getElementById('proofFrameWrap');
			var imageWrap = document.getElementById('proofImageWrap');
			var frame = document.getElementById('proofFrame');
			var image = document.getElementById('proofImage');
			var userEl = document.getElementById('proofUser');
			var openLink = document.getElementById('proofOpenLink');
			if (userEl) {
				userEl.textContent = user !== '' ? ('Usuário: ' + user) : '';
			}
			if (openLink) {
				openLink.href = url || '#';
				openLink.setAttribute('data-proof-url', url || '');
				openLink.style.pointerEvents = url ? 'auto' : 'none';
				openLink.style.opacity = url ? '1' : '0.5';
			}
			if (type === 'pdf' || type === 'auto') {
				if (frameWrap) { frameWrap.style.display = ''; }
				if (imageWrap) { imageWrap.style.display = 'none'; }
				if (frame) { frame.src = url; }
				if (image) { image.src = ''; }
			} else {
				if (frameWrap) { frameWrap.style.display = 'none'; }
				if (imageWrap) { imageWrap.style.display = ''; }
				if (image) { image.src = url; }
				if (frame) { frame.src = ''; }
			}
		});
		proofModal.addEventListener('hidden.bs.modal', function () {
			var frame = document.getElementById('proofFrame');
			var image = document.getElementById('proofImage');
			var openLink = document.getElementById('proofOpenLink');
			if (frame) { frame.src = ''; }
			if (image) { image.src = ''; }
			if (openLink) {
				openLink.href = '#';
				openLink.setAttribute('data-proof-url', '');
				openLink.style.pointerEvents = 'none';
				openLink.style.opacity = '0.5';
			}
		});
		var openLink = document.getElementById('proofOpenLink');
		if (openLink) {
			openLink.addEventListener('click', function (event) {
				var url = openLink.getAttribute('data-proof-url') || '';
				if (!url) {
					event.preventDefault();
					return;
				}
				window.open(url, '_blank', 'noopener');
			});
		}
	}
	var revokeModal = document.getElementById('revokeModal');
	if (revokeModal) {
		document.addEventListener('click', function (e) {
			var btn = e.target.closest('[data-action="open-refund"]');
			if (!btn) return;
			var id = btn.getAttribute('data-payment-id') || '';
			revokeModal.dataset.paymentId = id;
			revokeModal.dataset.user = btn.getAttribute('data-revoke-user') || '-';
			revokeModal.dataset.package = btn.getAttribute('data-revoke-package') || '-';
			revokeModal.dataset.months = btn.getAttribute('data-revoke-months') || '-';
			revokeModal.dataset.status = btn.getAttribute('data-revoke-status') || '-';
			revokeModal.dataset.created = btn.getAttribute('data-revoke-created') || '-';
			revokeModal.dataset.tier = btn.getAttribute('data-revoke-tier') || '-';
			revokeModal.dataset.subscription = btn.getAttribute('data-revoke-subscription') || '-';
			revokeModal.dataset.credits = btn.getAttribute('data-revoke-credits') || '0';
			revokeModal.dataset.proof = btn.getAttribute('data-revoke-proof') || '-';
			console.log('refund open payment_id:', id);
		});
		revokeModal.addEventListener('show.bs.modal', function () {
			var rawId = revokeModal.dataset.paymentId || '';
			var paymentId = parseInt(rawId, 10);
			console.log('revoke payment_id:', paymentId, 'raw:', rawId);
			var setText = function (id, value) {
				var el = document.getElementById(id);
				if (el) { el.textContent = value; }
			};
			var setValue = function (id, value) {
				var el = document.getElementById(id);
				if (el) { el.value = value; }
			};
			setValue('revokeId', Number.isFinite(paymentId) ? paymentId : 0);
			setText('revokeUser', revokeModal.dataset.user || '-');
			setText('revokePackage', revokeModal.dataset.package || '-');
			setText('revokeMonths', revokeModal.dataset.months || '-');
			setText('revokeStatus', revokeModal.dataset.status || '-');
			setText('revokeCreated', revokeModal.dataset.created || '-');
			setText('revokeTier', revokeModal.dataset.tier || '-');
			setText('revokeSubscription', revokeModal.dataset.subscription || '-');
			setText('revokeCredits', revokeModal.dataset.credits || '0');
			setText('revokeProof', revokeModal.dataset.proof || '-');
			var proofLink = document.getElementById('revokeProofLink');
			if (proofLink) {
				proofLink.style.display = 'none';
				proofLink.href = '#';
			}
			var form = revokeModal.querySelector('form');
			if (form && Number.isFinite(paymentId) && paymentId > 0) {
				var url = '<?= base_path('/admin/payments') ?>/' + paymentId + '/revoke';
				form.setAttribute('action', url);
				console.log('refund submit payment_id:', paymentId, 'url:', url);
			} else if (form) {
				form.setAttribute('action', '');
			}
			if (Number.isFinite(paymentId) && paymentId > 0) {
				fetch('<?= base_path('/admin/payments') ?>/' + paymentId + '/details', {
					method: 'GET',
					credentials: 'same-origin'
				})
					.then(function (res) { return res.json(); })
					.then(function (data) {
						if (!data || !data.ok || !data.payment) return;
						var p = data.payment;
						setText('revokeUser', p.username || '-');
						setText('revokePackage', p.package_title || ('#' + (p.package_id || '')));
						setText('revokeMonths', String(p.months || '-'));
						setText('revokeStatus', p.status || '-');
						setText('revokeCreated', p.created_at || '-');
						setText('revokeTier', p.access_tier || '-');
						setText('revokeSubscription', p.subscription_expires_at || '-');
						setText('revokeCredits', String(p.credits != null ? p.credits : '0'));
						if (p.proof_path) {
							setText('revokeProof', 'Sim');
							if (proofLink && p.proof_url) {
								proofLink.href = p.proof_url;
								proofLink.style.display = '';
							}
						} else {
							setText('revokeProof', 'Nao');
						}
					})
					.catch(function () {});
			}
		});
	}
	var revokeForm = revokeModal ? revokeModal.querySelector('form') : null;
	var revokeConfirmBtn = document.getElementById('revokeConfirmBtn');
	if (revokeForm && revokeConfirmBtn) {
		revokeConfirmBtn.addEventListener('click', function () {
			var rawId = revokeModal && revokeModal.dataset ? (revokeModal.dataset.paymentId || '') : '';
			var paymentId = parseInt(rawId, 10);
			if (!Number.isFinite(paymentId) || paymentId <= 0) {
				window.alert('ID invalido no estorno (sem payment_id).');
				return;
			}
			var formData = new FormData(revokeForm);
			var submitUrl = '<?= base_path('/admin/payments') ?>/' + paymentId + '/revoke';
			console.log('refund submit payment_id:', paymentId, 'url:', submitUrl);
			fetch(submitUrl, {
				method: 'POST',
				credentials: 'same-origin',
				body: formData
			})
				.then(function (res) { return res.json(); })
				.then(function (data) {
					if (data && data.ok) {
						if (window.bootstrap) {
							var modal = window.bootstrap.Modal.getInstance(revokeModal);
							if (modal) modal.hide();
						}
						window.location.reload();
						return;
					}
					var msg = (data && data.message) ? data.message : 'Falha ao estornar.';
					window.alert(msg);
				})
				.catch(function () {
					window.alert('Falha ao estornar.');
				});
		});
	}
	var revokeCancelModal = document.getElementById('revokeCancelModal');
	if (revokeCancelModal) {
		revokeCancelModal.addEventListener('show.bs.modal', function (event) {
			var button = event.relatedTarget || event.target;
			if (button && button.closest) {
				button = button.closest('button[data-revoke-id]');
			}
			if (!button) {
				return;
			}
			var setText = function (id, value) {
				var el = document.getElementById(id);
				if (el) { el.textContent = value; }
			};
			var setValue = function (id, value) {
				var el = document.getElementById(id);
				if (el) { el.value = value; }
			};
			setValue('revokeCancelId', button.getAttribute('data-revoke-id') || '');
			setText('revokeCancelUser', button.getAttribute('data-revoke-user') || '-');
			setText('revokeCancelPackage', button.getAttribute('data-revoke-package') || '-');
			setText('revokeCancelMonths', button.getAttribute('data-revoke-months') || '-');
			setText('revokeCancelStatus', button.getAttribute('data-revoke-status') || '-');
			setText('revokeCancelCreated', button.getAttribute('data-revoke-created') || '-');
			setText('revokeCancelTier', button.getAttribute('data-revoke-tier') || '-');
			setText('revokeCancelSubscription', button.getAttribute('data-revoke-subscription') || '-');
			setText('revokeCancelCredits', button.getAttribute('data-revoke-credits') || '0');
			setText('revokeCancelProof', button.getAttribute('data-revoke-proof') || '-');
		});
	}
});
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';