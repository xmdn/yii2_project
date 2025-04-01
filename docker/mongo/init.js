db.createCollection("user");
db.createCollection("task");
db.user.createIndex({ login: 1 }, { unique: true });
db.user.createIndex({ email: 1 }, { unique: true });
