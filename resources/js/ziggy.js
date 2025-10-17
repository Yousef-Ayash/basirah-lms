const Ziggy = {
    url: 'http:\/\/localhost:8000',
    port: 8000,
    defaults: {},
    routes: {
        'debugbar.openhandler': {
            uri: '_debugbar\/open',
            methods: ['GET', 'HEAD'],
        },
        'debugbar.clockwork': {
            uri: '_debugbar\/clockwork\/{id}',
            methods: ['GET', 'HEAD'],
            parameters: ['id'],
        },
        'debugbar.assets.css': {
            uri: '_debugbar\/assets\/stylesheets',
            methods: ['GET', 'HEAD'],
        },
        'debugbar.assets.js': {
            uri: '_debugbar\/assets\/javascript',
            methods: ['GET', 'HEAD'],
        },
        'debugbar.cache.delete': {
            uri: '_debugbar\/cache\/{key}\/{tags?}',
            methods: ['DELETE'],
            parameters: ['key', 'tags'],
        },
        'debugbar.queries.explain': {
            uri: '_debugbar\/queries\/explain',
            methods: ['POST'],
        },
        login: { uri: 'login', methods: ['GET', 'HEAD'] },
        logout: { uri: 'logout', methods: ['POST'] },
        'password.request': {
            uri: 'forgot-password',
            methods: ['GET', 'HEAD'],
        },
        'password.reset': {
            uri: 'reset-password\/{token}',
            methods: ['GET', 'HEAD'],
            parameters: ['token'],
        },
        'password.email': { uri: 'forgot-password', methods: ['POST'] },
        'password.store': { uri: 'reset-password', methods: ['POST'] },
        register: { uri: 'register', methods: ['GET', 'HEAD'] },
        'user-profile-information.update': {
            uri: 'user\/profile-information',
            methods: ['PUT'],
        },
        'user-password.update': { uri: 'user\/password', methods: ['PUT'] },
        'password.confirm': {
            uri: 'user\/confirm-password',
            methods: ['GET', 'HEAD'],
        },
        'password.confirmation': {
            uri: 'user\/confirmed-password-status',
            methods: ['GET', 'HEAD'],
        },
        'password.confirm.store': {
            uri: 'user\/confirm-password',
            methods: ['POST'],
        },
        'two-factor.login': {
            uri: 'two-factor-challenge',
            methods: ['GET', 'HEAD'],
        },
        'two-factor.login.store': {
            uri: 'two-factor-challenge',
            methods: ['POST'],
        },
        'two-factor.enable': {
            uri: 'user\/two-factor-authentication',
            methods: ['POST'],
        },
        'two-factor.confirm': {
            uri: 'user\/confirmed-two-factor-authentication',
            methods: ['POST'],
        },
        'two-factor.disable': {
            uri: 'user\/two-factor-authentication',
            methods: ['DELETE'],
        },
        'two-factor.qr-code': {
            uri: 'user\/two-factor-qr-code',
            methods: ['GET', 'HEAD'],
        },
        'two-factor.secret-key': {
            uri: 'user\/two-factor-secret-key',
            methods: ['GET', 'HEAD'],
        },
        'two-factor.recovery-codes': {
            uri: 'user\/two-factor-recovery-codes',
            methods: ['GET', 'HEAD'],
        },
        'two-factor.regenerate-recovery-codes': {
            uri: 'user\/two-factor-recovery-codes',
            methods: ['POST'],
        },
        'sanctum.csrf-cookie': {
            uri: 'sanctum\/csrf-cookie',
            methods: ['GET', 'HEAD'],
        },
        dashboard: { uri: 'dashboard', methods: ['GET', 'HEAD'] },
        'auth.approval.pending': {
            uri: 'approval-pending',
            methods: ['GET', 'HEAD'],
        },
        'profile.edit': { uri: 'profile', methods: ['GET', 'HEAD'] },
        'profile.update': { uri: 'profile', methods: ['PATCH'] },
        'profile.destroy': { uri: 'profile', methods: ['DELETE'] },
        'subjects.index': { uri: 'subjects', methods: ['GET', 'HEAD'] },
        'subjects.show': {
            uri: 'subjects\/{subject}',
            methods: ['GET', 'HEAD'],
            parameters: ['subject'],
            bindings: { subject: 'id' },
        },
        'materials.show': {
            uri: 'materials\/{material}',
            methods: ['GET', 'HEAD'],
            parameters: ['material'],
            bindings: { material: 'id' },
        },
        'bookmarks.index': { uri: 'bookmarks', methods: ['GET', 'HEAD'] },
        'bookmarks.store': { uri: 'bookmarks', methods: ['POST'] },
        'bookmarks.destroy': {
            uri: 'bookmarks\/{material}',
            methods: ['DELETE'],
            parameters: ['material'],
            bindings: { material: 'id' },
        },
        'exams.show': {
            uri: 'exams\/{exam}',
            methods: ['GET', 'HEAD'],
            parameters: ['exam'],
            bindings: { exam: 'id' },
        },
        'exams.start': {
            uri: 'exams\/{exam}\/start',
            methods: ['POST'],
            parameters: ['exam'],
            bindings: { exam: 'id' },
        },
        'attempts.index': { uri: 'my-attempts', methods: ['GET', 'HEAD'] },
        'attempts.autosave': {
            uri: 'attempts\/{attempt}\/autosave',
            methods: ['POST'],
            parameters: ['attempt'],
            bindings: { attempt: 'id' },
        },
        'attempts.submit': {
            uri: 'attempts\/{attempt}\/submit',
            methods: ['POST'],
            parameters: ['attempt'],
            bindings: { attempt: 'id' },
        },
        'attempts.export.xlsx': {
            uri: 'attempts\/export.xlsx',
            methods: ['GET', 'HEAD'],
        },
        'attempts.export.pdf': {
            uri: 'attempts\/export_ar.pdf',
            methods: ['GET', 'HEAD'],
        },
        'teachers.index': { uri: 'teachers', methods: ['GET', 'HEAD'] },
        'admin.users.pending': {
            uri: 'admin\/users\/pending',
            methods: ['GET', 'HEAD'],
        },
        'admin.users.approve': {
            uri: 'admin\/users\/{user}\/approve',
            methods: ['POST'],
            parameters: ['user'],
            bindings: { user: 'id' },
        },
        'admin.users.deny': {
            uri: 'admin\/users\/{user}\/deny',
            methods: ['POST'],
            parameters: ['user'],
            bindings: { user: 'id' },
        },
        'admin.subjects.index': {
            uri: 'admin\/subjects',
            methods: ['GET', 'HEAD'],
        },
        'admin.subjects.create': {
            uri: 'admin\/subjects\/create',
            methods: ['GET', 'HEAD'],
        },
        'admin.subjects.store': { uri: 'admin\/subjects', methods: ['POST'] },
        'admin.subjects.edit': {
            uri: 'admin\/subjects\/{subject}\/edit',
            methods: ['GET', 'HEAD'],
            parameters: ['subject'],
            bindings: { subject: 'id' },
        },
        'admin.subjects.update': {
            uri: 'admin\/subjects\/{subject}',
            methods: ['PUT', 'PATCH'],
            parameters: ['subject'],
            bindings: { subject: 'id' },
        },
        'admin.subjects.destroy': {
            uri: 'admin\/subjects\/{subject}',
            methods: ['DELETE'],
            parameters: ['subject'],
            bindings: { subject: 'id' },
        },
        'admin.subjects.materials.store': {
            uri: 'admin\/subjects\/{subject}\/materials',
            methods: ['POST'],
            parameters: ['subject'],
            bindings: { subject: 'id' },
        },
        'admin.materials.destroy': {
            uri: 'admin\/materials\/{material}',
            methods: ['DELETE'],
            parameters: ['material'],
            bindings: { material: 'id' },
        },
        'admin.exams.index': { uri: 'admin\/exams', methods: ['GET', 'HEAD'] },
        'admin.exams.create': {
            uri: 'admin\/exams\/create',
            methods: ['GET', 'HEAD'],
        },
        'admin.exams.store': { uri: 'admin\/exams', methods: ['POST'] },
        'admin.exams.show': {
            uri: 'admin\/exams\/{exam}',
            methods: ['GET', 'HEAD'],
            parameters: ['exam'],
            bindings: { exam: 'id' },
        },
        'admin.exams.edit': {
            uri: 'admin\/exams\/{exam}\/edit',
            methods: ['GET', 'HEAD'],
            parameters: ['exam'],
            bindings: { exam: 'id' },
        },
        'admin.exams.update': {
            uri: 'admin\/exams\/{exam}',
            methods: ['PUT', 'PATCH'],
            parameters: ['exam'],
            bindings: { exam: 'id' },
        },
        'admin.exams.destroy': {
            uri: 'admin\/exams\/{exam}',
            methods: ['DELETE'],
            parameters: ['exam'],
            bindings: { exam: 'id' },
        },
        'admin.exams.questions.export': {
            uri: 'admin\/exams\/{exam}\/questions\/export',
            methods: ['GET', 'HEAD'],
            parameters: ['exam'],
            bindings: { exam: 'id' },
        },
        'admin.exams.questions.index': {
            uri: 'admin\/exams\/{exam}\/questions',
            methods: ['GET', 'HEAD'],
            parameters: ['exam'],
            bindings: { exam: 'id' },
        },
        'admin.exams.questions.create': {
            uri: 'admin\/exams\/{exam}\/questions\/create',
            methods: ['GET', 'HEAD'],
            parameters: ['exam'],
            bindings: { exam: 'id' },
        },
        'admin.exams.questions.store': {
            uri: 'admin\/exams\/{exam}\/questions',
            methods: ['POST'],
            parameters: ['exam'],
            bindings: { exam: 'id' },
        },
        'admin.exams.questions.store.batch': {
            uri: 'admin\/exams\/{exam}\/questions\/batch-store',
            methods: ['POST'],
            parameters: ['exam'],
            bindings: { exam: 'id' },
        },
        'admin.exams.questions.edit': {
            uri: 'admin\/exams\/{exam}\/questions\/{question}\/edit',
            methods: ['GET', 'HEAD'],
            parameters: ['exam', 'question'],
            bindings: { exam: 'id', question: 'id' },
        },
        'admin.exams.questions.update': {
            uri: 'admin\/exams\/{exam}\/questions\/{question}',
            methods: ['PUT'],
            parameters: ['exam', 'question'],
            bindings: { exam: 'id', question: 'id' },
        },
        'admin.exams.questions.destroy.all': {
            uri: 'admin\/exams\/{exam}\/questions\/destroy-all',
            methods: ['DELETE'],
            parameters: ['exam'],
            bindings: { exam: 'id' },
        },
        'admin.exams.questions.destroy': {
            uri: 'admin\/exams\/{exam}\/questions\/{question}',
            methods: ['DELETE'],
            parameters: ['exam', 'question'],
            bindings: { exam: 'id', question: 'id' },
        },
        'admin.exams.questions.import.preview': {
            uri: 'admin\/exams\/{exam}\/questions\/import\/preview',
            methods: ['POST'],
            parameters: ['exam'],
            bindings: { exam: 'id' },
        },
        'admin.exams.questions.import.commit': {
            uri: 'admin\/exams\/{exam}\/questions\/import\/commit',
            methods: ['POST'],
            parameters: ['exam'],
            bindings: { exam: 'id' },
        },
        'admin.exams.attempts.index': {
            uri: 'admin\/exams\/{exam}\/attempts',
            methods: ['GET', 'HEAD'],
            parameters: ['exam'],
            bindings: { exam: 'id' },
        },
        'admin.exams.attempts.show': {
            uri: 'admin\/exams\/{exam}\/attempts\/{attempt}',
            methods: ['GET', 'HEAD'],
            parameters: ['exam', 'attempt'],
            bindings: { exam: 'id' },
        },
        'admin.logs.exam.index': {
            uri: 'admin\/logs\/exam',
            methods: ['GET', 'HEAD'],
        },
        'admin.logs.exam.export': {
            uri: 'admin\/logs\/exam\/export',
            methods: ['GET', 'HEAD'],
        },
        'admin.marks.index': { uri: 'admin\/marks', methods: ['GET', 'HEAD'] },
        'admin.marks.create': {
            uri: 'admin\/marks\/create',
            methods: ['GET', 'HEAD'],
        },
        'admin.marks.store': { uri: 'admin\/marks', methods: ['POST'] },
        'admin.marks.edit': {
            uri: 'admin\/marks\/{mark}\/edit',
            methods: ['GET', 'HEAD'],
            parameters: ['mark'],
            bindings: { mark: 'id' },
        },
        'admin.marks.update': {
            uri: 'admin\/marks\/{mark}',
            methods: ['PUT', 'PATCH'],
            parameters: ['mark'],
            bindings: { mark: 'id' },
        },
        'admin.marks.destroy': {
            uri: 'admin\/marks\/{mark}',
            methods: ['DELETE'],
            parameters: ['mark'],
            bindings: { mark: 'id' },
        },
        'admin.marks.import.preview': {
            uri: 'admin\/marks\/import\/preview',
            methods: ['POST'],
        },
        'admin.marks.import.commit': {
            uri: 'admin\/marks\/import\/commit',
            methods: ['POST'],
        },
        'admin.marks.import.form': {
            uri: 'admin\/marks\/import',
            methods: ['GET', 'HEAD'],
        },
        'admin.marks.export': {
            uri: 'admin\/marks\/export',
            methods: ['GET', 'HEAD'],
        },
        'admin.reports.index': {
            uri: 'admin\/reports',
            methods: ['GET', 'HEAD'],
        },
        'admin.reports.student_exam': {
            uri: 'admin\/reports\/student-exam',
            methods: ['GET', 'HEAD'],
        },
        'admin.reports.subject': {
            uri: 'admin\/reports\/subject',
            methods: ['GET', 'HEAD'],
        },
        'admin.reports.level': {
            uri: 'admin\/reports\/level',
            methods: ['GET', 'HEAD'],
        },
        'admin.reports.export': {
            uri: 'admin\/reports\/export',
            methods: ['GET', 'HEAD'],
        },
        'admin.reports.exams_for_student': {
            uri: 'admin\/reports\/exams-for-student\/{user}',
            methods: ['GET', 'HEAD'],
            parameters: ['user'],
            bindings: { user: 'id' },
        },
        'admin.students.export': {
            uri: 'admin\/students\/export',
            methods: ['GET', 'HEAD'],
        },
        'admin.students.index': {
            uri: 'admin\/students',
            methods: ['GET', 'HEAD'],
        },
        'admin.students.create': {
            uri: 'admin\/students\/create',
            methods: ['GET', 'HEAD'],
        },
        'admin.students.store': { uri: 'admin\/students', methods: ['POST'] },
        'admin.students.edit': {
            uri: 'admin\/students\/{student}\/edit',
            methods: ['GET', 'HEAD'],
            parameters: ['student'],
            bindings: { student: 'id' },
        },
        'admin.students.update': {
            uri: 'admin\/students\/{student}',
            methods: ['PUT', 'PATCH'],
            parameters: ['student'],
            bindings: { student: 'id' },
        },
        'admin.students.destroy': {
            uri: 'admin\/students\/{student}',
            methods: ['DELETE'],
            parameters: ['student'],
            bindings: { student: 'id' },
        },
        'admin.students.approve': {
            uri: 'admin\/students\/{user}\/approve',
            methods: ['POST'],
            parameters: ['user'],
            bindings: { user: 'id' },
        },
        'admin.students.deny': {
            uri: 'admin\/students\/{user}\/deny',
            methods: ['POST'],
            parameters: ['user'],
            bindings: { user: 'id' },
        },
        'admin.students.advance': {
            uri: 'admin\/students\/{user}\/advance',
            methods: ['POST'],
            parameters: ['user'],
            bindings: { user: 'id' },
        },
        'admin.students.import.form': {
            uri: 'admin\/students\/import',
            methods: ['GET', 'HEAD'],
        },
        'admin.students.import.preview': {
            uri: 'admin\/students\/import\/preview',
            methods: ['POST'],
        },
        'admin.students.import.commit': {
            uri: 'admin\/students\/import\/commit',
            methods: ['POST'],
        },
        'admin.view-as-student': {
            uri: 'admin\/view-as-student',
            methods: ['GET', 'HEAD'],
        },
        'admin.view-as-admin': {
            uri: 'admin\/view-as-admin',
            methods: ['GET', 'HEAD'],
        },
        'admin.teachers.index': {
            uri: 'admin\/teachers',
            methods: ['GET', 'HEAD'],
        },
        'admin.teachers.create': {
            uri: 'admin\/teachers\/create',
            methods: ['GET', 'HEAD'],
        },
        'admin.teachers.store': { uri: 'admin\/teachers', methods: ['POST'] },
        'admin.teachers.show': {
            uri: 'admin\/teachers\/{teacher}',
            methods: ['GET', 'HEAD'],
            parameters: ['teacher'],
        },
        'admin.teachers.edit': {
            uri: 'admin\/teachers\/{teacher}\/edit',
            methods: ['GET', 'HEAD'],
            parameters: ['teacher'],
            bindings: { teacher: 'id' },
        },
        'admin.teachers.update': {
            uri: 'admin\/teachers\/{teacher}',
            methods: ['PUT', 'PATCH'],
            parameters: ['teacher'],
            bindings: { teacher: 'id' },
        },
        'admin.teachers.destroy': {
            uri: 'admin\/teachers\/{teacher}',
            methods: ['DELETE'],
            parameters: ['teacher'],
            bindings: { teacher: 'id' },
        },
        'admin.levels.index': {
            uri: 'admin\/levels',
            methods: ['GET', 'HEAD'],
        },
        'admin.levels.create': {
            uri: 'admin\/levels\/create',
            methods: ['GET', 'HEAD'],
        },
        'admin.levels.store': { uri: 'admin\/levels', methods: ['POST'] },
        'admin.levels.edit': {
            uri: 'admin\/levels\/{level}\/edit',
            methods: ['GET', 'HEAD'],
            parameters: ['level'],
            bindings: { level: 'id' },
        },
        'admin.levels.update': {
            uri: 'admin\/levels\/{level}',
            methods: ['PUT', 'PATCH'],
            parameters: ['level'],
            bindings: { level: 'id' },
        },
        'admin.levels.destroy': {
            uri: 'admin\/levels\/{level}',
            methods: ['DELETE'],
            parameters: ['level'],
            bindings: { level: 'id' },
        },
        'verification.notice': {
            uri: 'verify-email',
            methods: ['GET', 'HEAD'],
        },
        'verification.verify': {
            uri: 'verify-email\/{id}\/{hash}',
            methods: ['GET', 'HEAD'],
            parameters: ['id', 'hash'],
        },
        'verification.send': {
            uri: 'email\/verification-notification',
            methods: ['POST'],
        },
        'password.update': { uri: 'password', methods: ['PUT'] },
        'storage.local': {
            uri: 'storage\/{path}',
            methods: ['GET', 'HEAD'],
            wheres: { path: '.*' },
            parameters: ['path'],
        },
    },
};
if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
    Object.assign(Ziggy.routes, window.Ziggy.routes);
}
export { Ziggy };
