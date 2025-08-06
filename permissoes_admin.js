const todasPermissoes = [
    "atendente-acesso_sistema",
    "caixa-abrir_caixa",
    "caixa-acesso_pdv",
    "caixa-excluir_item",
    "caixa-form_pgto",
    "caixa.abrir",
    "caixa.fechar",
    "caixa.relatorio",
    "caixa.sangria",
    "caixa.suprimento",
    "clientes.criar",
    "clientes.editar",
    "clientes.excluir",
    "clientes.listar",
    "clientes.visualizar",
    "configuracoes.backup",
    "configuracoes.empresa",
    "configuracoes.gerais",
    "configuracoes.impressao",
    "configuracoes.pdv",
    "configuracoes.seguranca",
    "configuracoes.sistema",
    "dashboard.relatorios",
    "dashboard.visualizar",
    "empresas.criar",
    "empresas.editar",
    "empresas.excluir",
    "empresas.listar",
    "empresas.visualizar",
    "estoque.ajustar",
    "estoque.relatorios",
    "estoque.transferir",
    "estoque.visualizar",
    "Finalizar venda",
    "financeiro.contas_pagar",
    "financeiro.contas_receber",
    "financeiro.fluxo_caixa",
    "financeiro.relatorios",
    "financeiro.visualizar",
    "horarios.criar",
    "horarios.editar",
    "horarios.excecoes.visualizar",
    "horarios.excluir",
    "horarios.listar",
    "horarios.padrao.visualizar",
    "horarios.visualizar",
    "marcas.criar",
    "marcas.editar",
    "marcas.excluir",
    "marcas.listar",
    "marcas.visualizar",
    "pdv.acessar",
    "pdv.adicionar_item",
    "pdv.aplicar_desconto",
    "pdv.cancelar_venda",
    "pdv.finalizar_venda",
    "pdv.iniciar_venda",
    "pdv.remover_item",
    "produtos.criar",
    "produtos.editar",
    "produtos.excluir",
    "produtos.gerenciar_estoque",
    "produtos.importar",
    "produtos.listar",
    "produtos.visualizar",
    "relatorios.avancados",
    "relatorios.clientes",
    "relatorios.estoque",
    "relatorios.financeiros",
    "relatorios.vendas",
    "sistema.admin",
    "sistema.logs",
    "sistema.manutencao",
    "usuarios.criar",
    "usuarios.editar",
    "usuarios.excluir",
    "usuarios.gerenciar_papeis",
    "usuarios.gerenciar_permissoes",
    "usuarios.listar",
    "usuarios.visualizar",
    "vendas.cancelar",
    "vendas.criar",
    "vendas.listar",
    "vendas.relatorios",
    "vendas.visualizar"
];

function toggleTodasPermissoes(isAdmin) {
    const checkboxes = document.querySelectorAll('input[name="permissoes[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = isAdmin;
        checkbox.disabled = isAdmin;
    });
}

// Adicionar event listener para o select de perfil
document.addEventListener('DOMContentLoaded', function() {
    const perfilSelect = document.querySelector('select[name="perfil"]');
    if (perfilSelect) {
        perfilSelect.addEventListener('change', function() {
            toggleTodasPermissoes(this.value === 'administrador');
        });
    }
});
