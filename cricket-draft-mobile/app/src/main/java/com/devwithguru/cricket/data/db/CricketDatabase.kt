package com.devwithguru.cricket.data.db

import androidx.room.Database
import androidx.room.RoomDatabase
import androidx.room.TypeConverters
import com.devwithguru.cricket.data.db.dao.BatterStatsDao
import com.devwithguru.cricket.data.db.dao.BowlerStatsDao
import com.devwithguru.cricket.data.db.dao.FixtureDao
import com.devwithguru.cricket.data.db.dao.InningsDao
import com.devwithguru.cricket.data.db.dao.PartnershipEventDao
import com.devwithguru.cricket.data.db.dao.PlayerDao
import com.devwithguru.cricket.data.db.dao.WicketEventDao
import com.devwithguru.cricket.data.db.entity.BatterStatsEntity
import com.devwithguru.cricket.data.db.entity.BowlerStatsEntity
import com.devwithguru.cricket.data.db.entity.FixtureEntity
import com.devwithguru.cricket.data.db.entity.InningsEntity
import com.devwithguru.cricket.data.db.entity.PartnershipEventEntity
import com.devwithguru.cricket.data.db.entity.PlayerEntity
import com.devwithguru.cricket.data.db.entity.WicketEventEntity

@Database(
    entities = [
        PlayerEntity::class,
        FixtureEntity::class,
        InningsEntity::class,
        BatterStatsEntity::class,
        BowlerStatsEntity::class,
        WicketEventEntity::class,
        PartnershipEventEntity::class
    ],
    version = 1,
    exportSchema = true
)
@TypeConverters(Converters::class)
abstract class CricketDatabase : RoomDatabase() {
    abstract fun playerDao(): PlayerDao
    abstract fun fixtureDao(): FixtureDao
    abstract fun inningsDao(): InningsDao
    abstract fun batterStatsDao(): BatterStatsDao
    abstract fun bowlerStatsDao(): BowlerStatsDao
    abstract fun wicketEventDao(): WicketEventDao
    abstract fun partnershipEventDao(): PartnershipEventDao
}
